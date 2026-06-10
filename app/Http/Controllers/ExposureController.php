<?php

namespace App\Http\Controllers;

use App\Application\DTO\ExposureData;
use App\Application\DTO\ExposureHierarchyItemData;
use App\Application\DTO\ExposureSessionBulkItemData;
use App\Application\DTO\ExposureSessionData;
use App\Application\UseCase\Exposure\AddHierarchyItemUseCase;
use App\Application\UseCase\Exposure\AddSessionUseCase;
use App\Application\UseCase\Exposure\CreateExposureUseCase;
use App\Application\UseCase\Exposure\DeleteExposureUseCase;
use App\Application\UseCase\Exposure\DeleteHierarchyItemUseCase;
use App\Application\UseCase\Exposure\DeleteSessionUseCase;
use App\Application\UseCase\Exposure\ExportExposureCsvUseCase;
use App\Application\UseCase\Exposure\SearchExposureUseCase;
use App\Application\UseCase\Exposure\SearchSessionUseCase;
use App\Application\UseCase\Exposure\SyncHierarchyItemsUseCase;
use App\Application\UseCase\Exposure\SyncSessionsUseCase;
use App\Application\UseCase\Exposure\UpdateExposureUseCase;
use App\Application\UseCase\Exposure\UpdateHierarchyItemUseCase;
use App\Application\UseCase\Exposure\UpdateSessionUseCase;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Exposure\AddHierarchyItemRequest;
use App\Http\Requests\Exposure\AddSessionRequest;
use App\Http\Requests\Exposure\CreateExposureRequest;
use App\Http\Requests\Exposure\SearchSessionRequest;
use App\Http\Requests\Exposure\SyncHierarchyItemsRequest;
use App\Http\Requests\Exposure\SyncSessionsRequest;
use App\Http\Requests\Exposure\UpdateExposureRequest;
use App\Http\Requests\Exposure\UpdateHierarchyItemRequest;
use App\Http\Requests\Exposure\UpdateSessionRequest;
use App\Infrastructure\Database\Models\Exposure;
use App\Infrastructure\Database\Models\ExposureHierarchyItem;
use App\Infrastructure\Database\Models\ExposureSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExposureController extends Controller
{
    public function index(SearchRequest $request, SearchExposureUseCase $searchUseCase): JsonResponse
    {
        $criteria = $request->toSearchCriteriaData();
        $exposures = $searchUseCase->handle($criteria);

        return response()->json($exposures);
    }

    public function show(Exposure $exposure): JsonResponse
    {
        $exposure->load(['hierarchyItems', 'sessions']);

        return response()->json($this->formatExposure($exposure));
    }

    public function sessions(SearchSessionRequest $request, SearchSessionUseCase $searchSessionUseCase): JsonResponse
    {
        $criteria = $request->toSessionSearchCriteriaData();
        $sessions = $searchSessionUseCase->handle($criteria);

        return response()->json($sessions);
    }

    public function showSession(ExposureSession $session): JsonResponse
    {
        $session->load(['exposure', 'hierarchyItem']);
        $exposure = $session->exposure;

        if ($exposure === null || $exposure->member_id !== (int) Auth::id()) {
            abort(404);
        }

        return response()->json([
            'id' => $session->id,
            'exposure_id' => $session->exposure_id,
            'avoidance_target' => $exposure->avoidance_target,
            'hierarchy_item_id' => $session->hierarchy_item_id,
            'hierarchy_item_content' => $session->hierarchyItem->content ?? '',
            'suds_after' => $session->suds_after,
            'reflection' => $session->reflection,
            'created_at' => $session->created_at->format(DATE_ATOM),
            'updated_at' => $session->updated_at->format(DATE_ATOM),
        ]);
    }

    public function store(CreateExposureRequest $request, CreateExposureUseCase $createExposure): JsonResponse
    {
        $exposureEntity = $createExposure->handle($this->toExposureData($request));

        $exposure = Exposure::with(['hierarchyItems', 'sessions'])
            ->where('member_id', (int) Auth::id())
            ->findOrFail($exposureEntity->getId());

        return response()->json($this->formatExposure($exposure), 201);
    }

    public function update(UpdateExposureRequest $request, Exposure $exposure, UpdateExposureUseCase $updateExposure): JsonResponse
    {
        $updateExposure->handle($exposure->id, $this->toExposureData($request));

        $exposure->refresh();
        $exposure->load(['hierarchyItems', 'sessions']);

        return response()->json($this->formatExposure($exposure));
    }

    public function destroy(Exposure $exposure, DeleteExposureUseCase $deleteExposure): JsonResponse
    {
        $deleteExposure->handle($exposure->id);

        return response()->json(null, 204);
    }

    public function syncHierarchyItems(
        SyncHierarchyItemsRequest $request,
        Exposure $exposure,
        SyncHierarchyItemsUseCase $syncHierarchyItems
    ): JsonResponse {
        try {
            $itemsData = array_map(
                fn (array $item) => new ExposureHierarchyItemData(
                    content: $item['content'],
                    sortOrder: (int) $item['sort_order'],
                    expectedSuds: isset($item['expected_suds']) ? (int) $item['expected_suds'] : null,
                    id: isset($item['id']) ? (int) $item['id'] : null
                ),
                $request->validated('items')
            );

            $items = $syncHierarchyItems->handle($exposure->id, $itemsData);
        } catch (\InvalidArgumentException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json([
            'items' => array_map(fn ($item) => [
                'id' => $item->getId(),
                'content' => $item->getContent(),
                'expected_suds' => $item->getExpectedSuds(),
                'sort_order' => $item->getSortOrder(),
            ], $items),
        ]);
    }

    public function syncSessions(
        SyncSessionsRequest $request,
        Exposure $exposure,
        SyncSessionsUseCase $syncSessions
    ): JsonResponse {
        try {
            $sessionsData = array_map(
                fn (array $session) => new ExposureSessionBulkItemData(
                    id: isset($session['id']) ? (int) $session['id'] : null,
                    hierarchyItemId: isset($session['hierarchy_item_id']) ? (int) $session['hierarchy_item_id'] : null,
                    actionPlan: $session['action_plan'] ?? null,
                    sudsBefore: isset($session['suds_before']) ? (int) $session['suds_before'] : null,
                    sudsPeak: isset($session['suds_peak']) ? (int) $session['suds_peak'] : null,
                    sudsAfter: isset($session['suds_after']) ? (int) $session['suds_after'] : null,
                    performedAt: $session['performed_at'] ?? null,
                    reflection: $session['reflection'] ?? null
                ),
                $request->validated('sessions')
            );

            $sessions = $syncSessions->handle($exposure->id, $sessionsData);
        } catch (\InvalidArgumentException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json([
            'sessions' => array_map(fn ($session) => [
                'id' => $session->getId(),
                'hierarchy_item_id' => $session->getHierarchyItemId(),
                'session_number' => $session->getSessionNumber(),
                'action_plan' => $session->getActionPlan(),
                'suds_before' => $session->getSudsBefore(),
                'suds_peak' => $session->getSudsPeak(),
                'suds_after' => $session->getSudsAfter(),
                'performed_at' => $session->getPerformedAt()?->format(DATE_ATOM),
                'reflection' => $session->getReflection(),
            ], $sessions),
        ]);
    }

    public function addHierarchyItem(AddHierarchyItemRequest $request, Exposure $exposure, AddHierarchyItemUseCase $addHierarchyItem): JsonResponse
    {
        $data = new ExposureHierarchyItemData(
            content: (string) $request->string('content'),
            sortOrder: (int) $request->integer('sort_order'),
            expectedSuds: $request->filled('expected_suds') ? (int) $request->integer('expected_suds') : null
        );

        $item = $addHierarchyItem->handle($exposure->id, $data);

        return response()->json([
            'id' => $item->getId(),
            'exposure_id' => $item->getExposureId(),
            'content' => $item->getContent(),
            'expected_suds' => $item->getExpectedSuds(),
            'sort_order' => $item->getSortOrder(),
            'created_at' => $item->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    public function updateHierarchyItem(
        UpdateHierarchyItemRequest $request,
        Exposure $exposure,
        ExposureHierarchyItem $hierarchyItem,
        UpdateHierarchyItemUseCase $updateHierarchyItem
    ): JsonResponse {
        if ($hierarchyItem->exposure_id !== $exposure->id) {
            abort(404);
        }

        $data = new ExposureHierarchyItemData(
            content: (string) $request->string('content'),
            sortOrder: (int) $request->integer('sort_order'),
            expectedSuds: $request->filled('expected_suds') ? (int) $request->integer('expected_suds') : null
        );

        $updated = $updateHierarchyItem->handle($hierarchyItem->id, $data);

        return response()->json([
            'id' => $updated->getId(),
            'exposure_id' => $updated->getExposureId(),
            'content' => $updated->getContent(),
            'expected_suds' => $updated->getExpectedSuds(),
            'sort_order' => $updated->getSortOrder(),
            'created_at' => $updated->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updated->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    public function deleteHierarchyItem(
        Exposure $exposure,
        ExposureHierarchyItem $hierarchyItem,
        DeleteHierarchyItemUseCase $deleteHierarchyItem
    ): JsonResponse {
        if ($hierarchyItem->exposure_id !== $exposure->id) {
            abort(404);
        }

        $deleteHierarchyItem->handle($hierarchyItem->id);

        return response()->json(null, 204);
    }

    public function addSession(AddSessionRequest $request, Exposure $exposure, AddSessionUseCase $addSession): JsonResponse
    {
        try {
            $data = $this->toSessionData($request);
            $session = $addSession->handle($exposure->id, $data);
        } catch (\InvalidArgumentException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json($this->formatSession($session), 201);
    }

    public function updateSession(
        UpdateSessionRequest $request,
        Exposure $exposure,
        ExposureSession $session,
        UpdateSessionUseCase $updateSession
    ): JsonResponse {
        if ($session->exposure_id !== $exposure->id) {
            abort(404);
        }

        try {
            $data = $this->toSessionData($request);
            $updated = $updateSession->handle($session->id, $data);
        } catch (\InvalidArgumentException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json($this->formatSession($updated));
    }

    public function deleteSession(
        Exposure $exposure,
        ExposureSession $session,
        DeleteSessionUseCase $deleteSession
    ): JsonResponse {
        if ($session->exposure_id !== $exposure->id) {
            abort(404);
        }

        $deleteSession->handle($session->id);

        return response()->json(null, 204);
    }

    public function exportCsv(SearchRequest $request, ExportExposureCsvUseCase $exportUseCase): StreamedResponse
    {
        $criteria = $request->toSearchCriteriaData();

        return $exportUseCase->handle($criteria);
    }

    private function toExposureData(CreateExposureRequest|UpdateExposureRequest $request): ExposureData
    {
        return new ExposureData(
            avoidanceTarget: (string) $request->string('avoidance_target'),
            selfTalk: $request->filled('self_talk') ? (string) $request->string('self_talk') : null,
            overallReflection: $request->filled('overall_reflection') ? (string) $request->string('overall_reflection') : null,
            nextGoal: $request->filled('next_goal') ? (string) $request->string('next_goal') : null
        );
    }

    private function toSessionData(AddSessionRequest $request): ExposureSessionData
    {
        return new ExposureSessionData(
            hierarchyItemId: $request->filled('hierarchy_item_id') ? (int) $request->integer('hierarchy_item_id') : null,
            actionPlan: $request->filled('action_plan') ? (string) $request->string('action_plan') : null,
            sudsBefore: $request->filled('suds_before') ? (int) $request->integer('suds_before') : null,
            sudsPeak: $request->filled('suds_peak') ? (int) $request->integer('suds_peak') : null,
            sudsAfter: $request->filled('suds_after') ? (int) $request->integer('suds_after') : null,
            performedAt: $request->filled('performed_at') ? (string) $request->string('performed_at') : null,
            reflection: $request->filled('reflection') ? (string) $request->string('reflection') : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function formatExposure(Exposure $exposure): array
    {
        return [
            'id' => $exposure->id,
            'avoidance_target' => $exposure->avoidance_target,
            'self_talk' => $exposure->self_talk,
            'overall_reflection' => $exposure->overall_reflection,
            'next_goal' => $exposure->next_goal,
            'hierarchy_items' => $exposure->hierarchyItems->map(fn ($item) => [
                'id' => $item->id,
                'content' => $item->content,
                'expected_suds' => $item->expected_suds,
                'sort_order' => $item->sort_order,
            ])->toArray(),
            'sessions' => $exposure->sessions->map(fn ($session) => [
                'id' => $session->id,
                'hierarchy_item_id' => $session->hierarchy_item_id,
                'session_number' => $session->session_number,
                'action_plan' => $session->action_plan,
                'suds_before' => $session->suds_before,
                'suds_peak' => $session->suds_peak,
                'suds_after' => $session->suds_after,
                'performed_at' => $session->performed_at?->format(DATE_ATOM),
                'reflection' => $session->reflection,
                'created_at' => $session->created_at->format(DATE_ATOM),
                'updated_at' => $session->updated_at->format(DATE_ATOM),
            ])->toArray(),
            'created_at' => $exposure->created_at->format(DATE_ATOM),
            'updated_at' => $exposure->updated_at->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatSession(\App\Domain\Entity\ExposureSession $session): array
    {
        return [
            'id' => $session->getId(),
            'exposure_id' => $session->getExposureId(),
            'hierarchy_item_id' => $session->getHierarchyItemId(),
            'session_number' => $session->getSessionNumber(),
            'action_plan' => $session->getActionPlan(),
            'suds_before' => $session->getSudsBefore(),
            'suds_peak' => $session->getSudsPeak(),
            'suds_after' => $session->getSudsAfter(),
            'performed_at' => $session->getPerformedAt()?->format(DATE_ATOM),
            'reflection' => $session->getReflection(),
            'created_at' => $session->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $session->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Application\DTO\ExposureData;
use App\Application\DTO\ExposureHierarchyItemData;
use App\Application\DTO\ExposureSessionBulkItemData;
use App\Application\DTO\ExposureSessionData;
use App\Application\Service\ExposureResponseFormatter;
use App\Application\UseCase\Exposure\AddHierarchyItemUseCase;
use App\Application\UseCase\Exposure\AddSessionUseCase;
use App\Application\UseCase\Exposure\CreateExposureUseCase;
use App\Application\UseCase\Exposure\DeleteExposureUseCase;
use App\Application\UseCase\Exposure\DeleteHierarchyItemUseCase;
use App\Application\UseCase\Exposure\DeleteSessionUseCase;
use App\Application\UseCase\Exposure\ExportExposureCsvUseCase;
use App\Application\UseCase\Exposure\SearchExposureUseCase;
use App\Application\UseCase\Exposure\SearchSessionUseCase;
use App\Application\UseCase\Exposure\ShowExposureUseCase;
use App\Application\UseCase\Exposure\ShowSessionUseCase;
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
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExposureController extends Controller
{
    public function __construct(private readonly ExposureResponseFormatter $formatter)
    {
    }

    public function index(SearchRequest $request, SearchExposureUseCase $searchUseCase): JsonResponse
    {
        $criteria = $request->toSearchCriteriaData();
        $exposures = $searchUseCase->handle($criteria);

        return response()->json($exposures);
    }

    public function show(Exposure $exposure, ShowExposureUseCase $showExposure): JsonResponse
    {
        return response()->json($showExposure->handle($exposure->id));
    }

    public function sessions(SearchSessionRequest $request, SearchSessionUseCase $searchSessionUseCase): JsonResponse
    {
        $criteria = $request->toSessionSearchCriteriaData();
        $sessions = $searchSessionUseCase->handle($criteria);

        return response()->json($sessions);
    }

    public function showSession(ExposureSession $session, ShowSessionUseCase $showSession): JsonResponse
    {
        return response()->json($showSession->handle($session));
    }

    public function store(CreateExposureRequest $request, CreateExposureUseCase $createExposure): JsonResponse
    {
        $exposureEntity = $createExposure->handle($this->toExposureData($request));

        return response()->json($this->formatter->exposureFromEntity($exposureEntity), 201);
    }

    public function update(UpdateExposureRequest $request, Exposure $exposure, UpdateExposureUseCase $updateExposure): JsonResponse
    {
        $exposureEntity = $updateExposure->handle($exposure->id, $this->toExposureData($request));

        return response()->json($this->formatter->exposureFromEntity($exposureEntity));
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
            'items' => array_map(
                fn ($item) => $this->formatter->hierarchyItemFromEntity($item),
                $items
            ),
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
                    sudsAfter: isset($session['suds_after']) ? (int) $session['suds_after'] : null,
                    reflection: $session['reflection'] ?? null
                ),
                $request->validated('sessions')
            );

            $sessions = $syncSessions->handle($exposure->id, $sessionsData);
        } catch (\InvalidArgumentException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json([
            'sessions' => array_map(
                fn ($session) => $this->formatter->sessionFromEntity($session),
                $sessions
            ),
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

        return response()->json(array_merge(
            $this->formatter->hierarchyItemFromEntity($item),
            [
                'exposure_id' => $item->getExposureId(),
                'created_at' => $item->getCreatedAt()->format(DATE_ATOM),
                'updated_at' => $item->getUpdatedAt()->format(DATE_ATOM),
            ]
        ), 201);
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

        return response()->json(array_merge(
            $this->formatter->hierarchyItemFromEntity($updated),
            [
                'exposure_id' => $updated->getExposureId(),
                'created_at' => $updated->getCreatedAt()->format(DATE_ATOM),
                'updated_at' => $updated->getUpdatedAt()->format(DATE_ATOM),
            ]
        ));
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

        return response()->json($this->formatter->sessionFromEntity($session), 201);
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

        return response()->json($this->formatter->sessionFromEntity($updated));
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
            avoidanceTarget: (string) $request->string('avoidance_target')
        );
    }

    private function toSessionData(AddSessionRequest|UpdateSessionRequest $request): ExposureSessionData
    {
        return new ExposureSessionData(
            hierarchyItemId: $request->filled('hierarchy_item_id') ? (int) $request->integer('hierarchy_item_id') : null,
            sudsAfter: $request->filled('suds_after') ? (int) $request->integer('suds_after') : null,
            reflection: $request->filled('reflection') ? (string) $request->string('reflection') : null
        );
    }
}

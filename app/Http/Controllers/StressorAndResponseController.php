<?php

namespace App\Http\Controllers;

use App\Application\DTO\StressorAndResponseData;
use App\Application\UseCase\StressorAndResponse\CreateStressorAndResponseUseCase;
use App\Application\UseCase\StressorAndResponse\DeleteStressorAndResponseUseCase;
use App\Application\UseCase\StressorAndResponse\ExportStressorAndResponseCsvUseCase;
use App\Application\UseCase\StressorAndResponse\SearchStressorAndResponseUseCase;
use App\Application\UseCase\StressorAndResponse\UpdateStressorAndResponseUseCase;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\StressorAndResponse\CreateStressorAndResponseRequest;
use App\Http\Requests\StressorAndResponse\UpdateStressorAndResponseRequest;
use App\Infrastructure\Database\Models\StressorAndResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StressorAndResponseController extends Controller
{
    /**
     * ストレッサーとストレス反応一覧を取得（作成日時降順）
     * キーワード検索とタグ検索に対応
     */
    public function index(SearchRequest $request, SearchStressorAndResponseUseCase $searchUseCase): JsonResponse
    {
        $criteria = $request->toSearchCriteriaData();
        $items = $searchUseCase->handle($criteria);

        return response()->json($items);
    }

    /**
     * ストレッサーとストレス反応詳細を取得
     */
    public function show(StressorAndResponse $stressorAndResponse): JsonResponse
    {
        $stressorAndResponse->load('tags');

        return response()->json([
            'id' => $stressorAndResponse->id,
            'stressor' => $stressorAndResponse->stressor,
            'cognition' => $stressorAndResponse->cognition,
            'mood' => $stressorAndResponse->mood,
            'body_reaction' => $stressorAndResponse->body_reaction,
            'behavior' => $stressorAndResponse->behavior,
            'stimulated_schemas' => $stressorAndResponse->stimulated_schemas,
            'tags' => $stressorAndResponse->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $stressorAndResponse->tags->pluck('id')->toArray(),
            'created_at' => $stressorAndResponse->created_at->format(DATE_ATOM),
            'updated_at' => $stressorAndResponse->updated_at->format(DATE_ATOM),
        ]);
    }

    /**
     * ストレッサーとストレス反応を作成
     */
    public function store(CreateStressorAndResponseRequest $request, CreateStressorAndResponseUseCase $createUseCase): JsonResponse
    {
        $stimulatedSchemas = $request->has('stimulated_schemas') && is_array($request->input('stimulated_schemas'))
            ? $request->input('stimulated_schemas')
            : null;

        $data = new StressorAndResponseData(
            stressor: (string) $request->string('stressor'),
            cognition: $request->has('cognition') && $request->filled('cognition') ? (string) $request->string('cognition') : null,
            mood: $request->has('mood') && $request->filled('mood') ? (string) $request->string('mood') : null,
            bodyReaction: $request->has('body_reaction') && $request->filled('body_reaction') ? (string) $request->string('body_reaction') : null,
            behavior: $request->has('behavior') && $request->filled('behavior') ? (string) $request->string('behavior') : null,
            stimulatedSchemas: $stimulatedSchemas
        );

        $item = $createUseCase->handle($data);

        // タグの紐付け
        $tagIds = $request->input('tag_ids', []);
        if (!empty($tagIds)) {
            $model = StressorAndResponse::find($item->getId());
            $model->tags()->sync($tagIds);
            $model->load('tags');
        }

        $model = StressorAndResponse::with('tags')->find($item->getId());

        return response()->json([
            'id' => $item->getId(),
            'stressor' => $item->getStressor(),
            'cognition' => $item->getCognition(),
            'mood' => $item->getMood(),
            'body_reaction' => $item->getBodyReaction(),
            'behavior' => $item->getBehavior(),
            'stimulated_schemas' => $item->getStimulatedSchemas(),
            'tags' => $model->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $model->tags->pluck('id')->toArray(),
            'created_at' => $item->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * ストレッサーとストレス反応を更新
     */
    public function update(UpdateStressorAndResponseRequest $request, StressorAndResponse $stressorAndResponse, UpdateStressorAndResponseUseCase $updateUseCase): JsonResponse
    {
        $stimulatedSchemas = $request->has('stimulated_schemas') && is_array($request->input('stimulated_schemas'))
            ? $request->input('stimulated_schemas')
            : null;

        $data = new StressorAndResponseData(
            stressor: (string) $request->string('stressor'),
            cognition: $request->has('cognition') && $request->filled('cognition') ? (string) $request->string('cognition') : null,
            mood: $request->has('mood') && $request->filled('mood') ? (string) $request->string('mood') : null,
            bodyReaction: $request->has('body_reaction') && $request->filled('body_reaction') ? (string) $request->string('body_reaction') : null,
            behavior: $request->has('behavior') && $request->filled('behavior') ? (string) $request->string('behavior') : null,
            stimulatedSchemas: $stimulatedSchemas
        );

        $updatedItem = $updateUseCase->handle($stressorAndResponse->id, $data);

        // タグの同期
        $tagIds = $request->input('tag_ids', []);
        $stressorAndResponse->tags()->sync($tagIds);
        $stressorAndResponse->load('tags');

        return response()->json([
            'id' => $updatedItem->getId(),
            'stressor' => $updatedItem->getStressor(),
            'cognition' => $updatedItem->getCognition(),
            'mood' => $updatedItem->getMood(),
            'body_reaction' => $updatedItem->getBodyReaction(),
            'behavior' => $updatedItem->getBehavior(),
            'stimulated_schemas' => $updatedItem->getStimulatedSchemas(),
            'tags' => $stressorAndResponse->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $stressorAndResponse->tags->pluck('id')->toArray(),
            'created_at' => $updatedItem->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedItem->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * ストレッサーとストレス反応を削除
     */
    public function destroy(StressorAndResponse $stressorAndResponse, DeleteStressorAndResponseUseCase $deleteUseCase): JsonResponse
    {
        $deleteUseCase->handle($stressorAndResponse->id);

        return response()->json(null, 204);
    }

    /**
     * ストレッサーとストレス反応をCSV形式でエクスポート
     */
    public function exportCsv(SearchRequest $request, ExportStressorAndResponseCsvUseCase $exportUseCase): StreamedResponse
    {
        $criteria = $request->toSearchCriteriaData();

        return $exportUseCase->handle($criteria);
    }
}

<?php

namespace App\Http\Controllers;

use App\Application\DTO\StressorAndResponseData;
use App\Application\UseCase\StressorAndResponse\CreateStressorAndResponseUseCase;
use App\Application\UseCase\StressorAndResponse\DeleteStressorAndResponseUseCase;
use App\Application\UseCase\StressorAndResponse\UpdateStressorAndResponseUseCase;
use App\Http\Requests\StressorAndResponse\CreateStressorAndResponseRequest;
use App\Http\Requests\StressorAndResponse\UpdateStressorAndResponseRequest;
use App\Infrastructure\Database\Models\StressorAndResponse;
use Illuminate\Http\JsonResponse;

class StressorAndResponseController extends Controller
{
    /**
     * ストレッサーとストレス反応一覧を取得（作成日時降順）
     */
    public function index(): JsonResponse
    {
        $items = StressorAndResponse::orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'stressor' => $item->stressor,
                    'cognition' => $item->cognition,
                    'mood' => $item->mood,
                    'body_reaction' => $item->body_reaction,
                    'behavior' => $item->behavior,
                    'created_at' => $item->created_at->format(DATE_ATOM),
                    'updated_at' => $item->updated_at->format(DATE_ATOM),
                ];
            });

        return response()->json($items);
    }

    /**
     * ストレッサーとストレス反応詳細を取得
     */
    public function show(StressorAndResponse $stressorAndResponse): JsonResponse
    {
        return response()->json([
            'id' => $stressorAndResponse->id,
            'stressor' => $stressorAndResponse->stressor,
            'cognition' => $stressorAndResponse->cognition,
            'mood' => $stressorAndResponse->mood,
            'body_reaction' => $stressorAndResponse->body_reaction,
            'behavior' => $stressorAndResponse->behavior,
            'created_at' => $stressorAndResponse->created_at->format(DATE_ATOM),
            'updated_at' => $stressorAndResponse->updated_at->format(DATE_ATOM),
        ]);
    }

    /**
     * ストレッサーとストレス反応を作成
     */
    public function store(CreateStressorAndResponseRequest $request, CreateStressorAndResponseUseCase $createUseCase): JsonResponse
    {
        $data = new StressorAndResponseData(
            stressor: (string) $request->string('stressor'),
            cognition: $request->has('cognition') && $request->filled('cognition') ? (string) $request->string('cognition') : null,
            mood: $request->has('mood') && $request->filled('mood') ? (string) $request->string('mood') : null,
            bodyReaction: $request->has('body_reaction') && $request->filled('body_reaction') ? (string) $request->string('body_reaction') : null,
            behavior: $request->has('behavior') && $request->filled('behavior') ? (string) $request->string('behavior') : null
        );

        $item = $createUseCase->handle($data);

        return response()->json([
            'id' => $item->getId(),
            'stressor' => $item->getStressor(),
            'cognition' => $item->getCognition(),
            'mood' => $item->getMood(),
            'body_reaction' => $item->getBodyReaction(),
            'behavior' => $item->getBehavior(),
            'created_at' => $item->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * ストレッサーとストレス反応を更新
     */
    public function update(UpdateStressorAndResponseRequest $request, StressorAndResponse $stressorAndResponse, UpdateStressorAndResponseUseCase $updateUseCase): JsonResponse
    {
        $data = new StressorAndResponseData(
            stressor: (string) $request->string('stressor'),
            cognition: $request->has('cognition') && $request->filled('cognition') ? (string) $request->string('cognition') : null,
            mood: $request->has('mood') && $request->filled('mood') ? (string) $request->string('mood') : null,
            bodyReaction: $request->has('body_reaction') && $request->filled('body_reaction') ? (string) $request->string('body_reaction') : null,
            behavior: $request->has('behavior') && $request->filled('behavior') ? (string) $request->string('behavior') : null
        );

        $updatedItem = $updateUseCase->handle($stressorAndResponse->id, $data);

        return response()->json([
            'id' => $updatedItem->getId(),
            'stressor' => $updatedItem->getStressor(),
            'cognition' => $updatedItem->getCognition(),
            'mood' => $updatedItem->getMood(),
            'body_reaction' => $updatedItem->getBodyReaction(),
            'behavior' => $updatedItem->getBehavior(),
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
}

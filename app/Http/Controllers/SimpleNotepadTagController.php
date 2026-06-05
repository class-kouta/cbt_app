<?php

namespace App\Http\Controllers;

use App\Application\DTO\SimpleNotepadTagData;
use App\Application\UseCase\SimpleNotepadTag\CreateSimpleNotepadTagUseCase;
use App\Application\UseCase\SimpleNotepadTag\DeleteSimpleNotepadTagUseCase;
use App\Application\UseCase\SimpleNotepadTag\ListSimpleNotepadTagsUseCase;
use App\Application\UseCase\SimpleNotepadTag\UpdateSimpleNotepadTagUseCase;
use App\Http\Requests\SimpleNotepadTag\CreateSimpleNotepadTagRequest;
use App\Http\Requests\SimpleNotepadTag\UpdateSimpleNotepadTagRequest;
use App\Infrastructure\Database\Models\SimpleNotepadTag;
use DomainException;
use Illuminate\Http\JsonResponse;

class SimpleNotepadTagController extends Controller
{
    /**
     * メモ帳タグ一覧を取得
     */
    public function index(ListSimpleNotepadTagsUseCase $listSimpleNotepadTags): JsonResponse
    {
        $tags = collect($listSimpleNotepadTags->handle())
            ->map(function ($tag) {
                return [
                    'id' => $tag->getId(),
                    'name' => $tag->getName(),
                    'created_at' => $tag->getCreatedAt()->format(DATE_ATOM),
                    'updated_at' => $tag->getUpdatedAt()->format(DATE_ATOM),
                ];
            });

        return response()->json($tags);
    }

    /**
     * メモ帳タグを作成
     */
    public function store(
        CreateSimpleNotepadTagRequest $request,
        CreateSimpleNotepadTagUseCase $createSimpleNotepadTag
    ): JsonResponse {
        try {
            $data = new SimpleNotepadTagData(
                name: (string) $request->string('name')
            );

            $tag = $createSimpleNotepadTag->handle($data);

            return response()->json([
                'id' => $tag->getId(),
                'name' => $tag->getName(),
                'created_at' => $tag->getCreatedAt()->format(DATE_ATOM),
                'updated_at' => $tag->getUpdatedAt()->format(DATE_ATOM),
            ], 201);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * メモ帳タグを更新
     */
    public function update(
        UpdateSimpleNotepadTagRequest $request,
        SimpleNotepadTag $simpleNotepadTag,
        UpdateSimpleNotepadTagUseCase $updateSimpleNotepadTag
    ): JsonResponse {
        try {
            $data = new SimpleNotepadTagData(
                name: (string) $request->string('name')
            );

            $updatedTag = $updateSimpleNotepadTag->handle($simpleNotepadTag->id, $data);

            return response()->json([
                'id' => $updatedTag->getId(),
                'name' => $updatedTag->getName(),
                'created_at' => $updatedTag->getCreatedAt()->format(DATE_ATOM),
                'updated_at' => $updatedTag->getUpdatedAt()->format(DATE_ATOM),
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * メモ帳タグを削除
     */
    public function destroy(
        SimpleNotepadTag $simpleNotepadTag,
        DeleteSimpleNotepadTagUseCase $deleteSimpleNotepadTag
    ): JsonResponse {
        try {
            $deleteSimpleNotepadTag->handle($simpleNotepadTag->id);

            return response()->json(null, 204);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}

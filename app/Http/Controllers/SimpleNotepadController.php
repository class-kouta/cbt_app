<?php

namespace App\Http\Controllers;

use App\Application\DTO\SimpleNotepadData;
use App\Application\UseCase\SimpleNotepad\CreateSimpleNotepadUseCase;
use App\Application\UseCase\SimpleNotepad\DeleteSimpleNotepadUseCase;
use App\Application\UseCase\SimpleNotepad\SearchSimpleNotepadUseCase;
use App\Application\UseCase\SimpleNotepad\UpdateSimpleNotepadUseCase;
use App\Http\Requests\SimpleNotepad\CreateSimpleNotepadRequest;
use App\Http\Requests\SimpleNotepad\SimpleNotepadSearchRequest;
use App\Http\Requests\SimpleNotepad\UpdateSimpleNotepadRequest;
use App\Infrastructure\Database\Models\SimpleNotepad;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SimpleNotepadController extends Controller
{
    /**
     * メモ帳一覧を取得（作成日時降順）
     * キーワード検索とタグ検索に対応
     */
    public function index(SimpleNotepadSearchRequest $request, SearchSimpleNotepadUseCase $searchUseCase): JsonResponse
    {
        $criteria = $request->toSearchCriteriaData();
        $simpleNotepads = $searchUseCase->handle($criteria);

        return response()->json($simpleNotepads);
    }

    /**
     * メモ帳詳細を取得
     */
    public function show(SimpleNotepad $simpleNotepad): JsonResponse
    {
        $simpleNotepad->load('tags');

        return response()->json([
            'id' => $simpleNotepad->id,
            'content' => $simpleNotepad->content,
            'tags' => $simpleNotepad->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $simpleNotepad->tags->pluck('id')->toArray(),
            'created_at' => $simpleNotepad->created_at->format(DATE_ATOM),
            'updated_at' => $simpleNotepad->updated_at->format(DATE_ATOM),
        ]);
    }

    /**
     * メモ帳を作成
     */
    public function store(
        CreateSimpleNotepadRequest $request,
        CreateSimpleNotepadUseCase $createSimpleNotepad
    ): JsonResponse {
        $data = new SimpleNotepadData(
            content: (string) $request->string('content'),
            tagIds: $request->input('tag_ids', []),
        );

        $result = $createSimpleNotepad->handle($data, (int) Auth::id());

        return response()->json($result, 201);
    }

    /**
     * メモ帳を更新
     */
    public function update(
        UpdateSimpleNotepadRequest $request,
        SimpleNotepad $simpleNotepad,
        UpdateSimpleNotepadUseCase $updateSimpleNotepad
    ): JsonResponse {
        $data = new SimpleNotepadData(
            content: (string) $request->string('content'),
            tagIds: $request->input('tag_ids', []),
        );

        $result = $updateSimpleNotepad->handle($simpleNotepad->id, $data, (int) Auth::id());

        return response()->json($result);
    }

    /**
     * メモ帳を削除
     */
    public function destroy(
        SimpleNotepad $simpleNotepad,
        DeleteSimpleNotepadUseCase $deleteSimpleNotepad
    ): JsonResponse {
        $deleteSimpleNotepad->handle($simpleNotepad->id);

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Application\DTO\SimpleNotepadData;
use App\Application\UseCase\SimpleNotepad\CreateSimpleNotepadUseCase;
use App\Application\UseCase\SimpleNotepad\DeleteSimpleNotepadUseCase;
use App\Application\UseCase\SimpleNotepad\ListSimpleNotepadsUseCase;
use App\Application\UseCase\SimpleNotepad\UpdateSimpleNotepadUseCase;
use App\Http\Requests\SimpleNotepad\CreateSimpleNotepadRequest;
use App\Http\Requests\SimpleNotepad\UpdateSimpleNotepadRequest;
use App\Infrastructure\Database\Models\SimpleNotepad;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SimpleNotepadController extends Controller
{
    /**
     * メモ帳一覧を取得（作成日時降順）
     */
    public function index(ListSimpleNotepadsUseCase $listSimpleNotepads): JsonResponse
    {
        return response()->json($listSimpleNotepads->handle((int) Auth::id()));
    }

    /**
     * メモ帳を作成
     */
    public function store(
        CreateSimpleNotepadRequest $request,
        CreateSimpleNotepadUseCase $createSimpleNotepad
    ): JsonResponse {
        $data = new SimpleNotepadData(
            title: (string) $request->string('title'),
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
            title: (string) $request->string('title'),
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

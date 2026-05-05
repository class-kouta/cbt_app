<?php

namespace App\Http\Controllers;

use App\Application\DTO\SimpleNotepadData;
use App\Application\UseCase\SimpleNotepad\CreateSimpleNotepadUseCase;
use App\Application\UseCase\SimpleNotepad\UpdateSimpleNotepadUseCase;
use App\Application\UseCase\SimpleNotepad\DeleteSimpleNotepadUseCase;
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
    public function index(): JsonResponse
    {
        $simpleNotepads = SimpleNotepad::where('member_id', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($simpleNotepad) {
                return [
                    'id' => $simpleNotepad->id,
                    'title' => $simpleNotepad->title,
                    'content' => $simpleNotepad->content,
                    'created_at' => $simpleNotepad->created_at->format(DATE_ATOM),
                    'updated_at' => $simpleNotepad->updated_at->format(DATE_ATOM),
                ];
            });

        return response()->json($simpleNotepads);
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
            content: (string) $request->string('content')
        );

        $simpleNotepad = $createSimpleNotepad->handle($data);

        return response()->json([
            'id' => $simpleNotepad->getId(),
            'title' => $simpleNotepad->getTitle(),
            'content' => $simpleNotepad->getContent(),
            'created_at' => $simpleNotepad->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $simpleNotepad->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
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
            content: (string) $request->string('content')
        );

        $updatedSimpleNotepad = $updateSimpleNotepad->handle($simpleNotepad->id, $data);

        return response()->json([
            'id' => $updatedSimpleNotepad->getId(),
            'title' => $updatedSimpleNotepad->getTitle(),
            'content' => $updatedSimpleNotepad->getContent(),
            'created_at' => $updatedSimpleNotepad->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedSimpleNotepad->getUpdatedAt()->format(DATE_ATOM),
        ]);
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

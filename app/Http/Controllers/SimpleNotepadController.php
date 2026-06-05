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
        $memberId = (int) Auth::id();
        $simpleNotepads = collect($listSimpleNotepads->handle());
        $ids = $simpleNotepads->map(fn ($item) => $item->getId())->all();

        $modelsById = SimpleNotepad::where('member_id', $memberId)
            ->with('tags')
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $response = $simpleNotepads->map(function ($simpleNotepad) use ($modelsById) {
            $model = $modelsById->get($simpleNotepad->getId());

            return $this->formatResponse($simpleNotepad, $model);
        });

        return response()->json($response);
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

        $tagIds = $request->input('tag_ids', []);
        $model = SimpleNotepad::where('member_id', (int) Auth::id())
            ->with('tags')
            ->findOrFail($simpleNotepad->getId());

        if (! empty($tagIds)) {
            $model->tags()->sync($tagIds);
            $model->load('tags');
        }

        return response()->json($this->formatResponse($simpleNotepad, $model), 201);
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

        $tagIds = $request->input('tag_ids', []);
        $simpleNotepad->tags()->sync($tagIds);
        $simpleNotepad->load('tags');

        return response()->json($this->formatResponse($updatedSimpleNotepad, $simpleNotepad));
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

    private function formatResponse($simpleNotepad, ?SimpleNotepad $model): array
    {
        $tags = $model?->tags ?? collect();

        return [
            'id' => $simpleNotepad->getId(),
            'title' => $simpleNotepad->getTitle(),
            'content' => $simpleNotepad->getContent(),
            'tags' => $tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->values()->toArray(),
            'tag_ids' => $tags->pluck('id')->values()->toArray(),
            'created_at' => $simpleNotepad->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $simpleNotepad->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Application\DTO\TodoData;
use App\Application\UseCase\Todo\CreateTodoUseCase;
use App\Application\UseCase\Todo\UncompleteTodoUseCase;
use App\Http\Requests\Todo\CreateTodoRequest;
use App\Infrastructure\Database\Models\Todo;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    /**
     * TODO一覧を取得（未完了のみ）
     */
    public function index(): JsonResponse
    {
        $todos = Todo::with(['difficulty', 'tags'])
            ->whereNull('completed_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($todo) {
                return [
                    'id' => $todo->id,
                    'difficulty_id' => $todo->difficulty_id,
                    'difficulty' => $todo->difficulty ? [
                        'id' => $todo->difficulty->id,
                        'name' => $todo->difficulty->name,
                        'points' => $todo->difficulty->points,
                        'color' => $todo->difficulty->color,
                    ] : null,
                    'content' => $todo->content,
                    'completed_at' => $todo->completed_at?->format(DATE_ATOM),
                    'created_at' => $todo->created_at->format(DATE_ATOM),
                    'updated_at' => $todo->updated_at->format(DATE_ATOM),
                    'tags' => $todo->tags->map(function ($tag) {
                        return [
                            'id' => $tag->id,
                            'name' => $tag->name,
                        ];
                    }),
                ];
            });

        return response()->json($todos);
    }

    /**
     * TODOを作成
     */
    public function store(CreateTodoRequest $request, CreateTodoUseCase $createTodo): JsonResponse
    {
        $data = new TodoData(
            difficultyId: (int) $request->integer('difficulty_id'),
            content: (string) $request->string('content'),
            tagIds: (array) $request->input('tag_ids', [])
        );

        $todo = $createTodo->handle($data);

        return response()->json([
            'id' => $todo->getId(),
            'difficulty_id' => $todo->getDifficultyId(),
            'content' => $todo->getContent(),
            'completed_at' => $todo->getCompletedAt()?->format(DATE_ATOM),
            'created_at' => $todo->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $todo->getUpdatedAt()->format(DATE_ATOM),
            'tag_ids' => $todo->getTagIds(),
        ], 201);
    }

    /**
     * TODOを完了にする
     */
    public function complete(Todo $todo): JsonResponse
    {
        $todo->update([
            'completed_at' => now(),
        ]);

        $todo->load(['difficulty', 'tags']);

        return response()->json([
            'id' => $todo->id,
            'difficulty_id' => $todo->difficulty_id,
            'difficulty' => $todo->difficulty ? [
                'id' => $todo->difficulty->id,
                'name' => $todo->difficulty->name,
                'points' => $todo->difficulty->points,
                'color' => $todo->difficulty->color,
            ] : null,
            'content' => $todo->content,
            'completed_at' => $todo->completed_at?->format(DATE_ATOM),
            'created_at' => $todo->created_at->format(DATE_ATOM),
            'updated_at' => $todo->updated_at->format(DATE_ATOM),
            'tags' => $todo->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            }),
        ]);
    }

    /**
     * TODOを未完了に戻す
     */
    public function uncomplete(Todo $todo, UncompleteTodoUseCase $uncomplete): JsonResponse
    {
        $uncomplete->handle($todo->id);

        $todo->refresh();
        $todo->load(['difficulty', 'tags']);

        return response()->json([
            'id' => $todo->id,
            'difficulty_id' => $todo->difficulty_id,
            'difficulty' => $todo->difficulty ? [
                'id' => $todo->difficulty->id,
                'name' => $todo->difficulty->name,
                'points' => $todo->difficulty->points,
                'color' => $todo->difficulty->color,
            ] : null,
            'content' => $todo->content,
            'completed_at' => $todo->completed_at?->format(DATE_ATOM),
            'created_at' => $todo->created_at->format(DATE_ATOM),
            'updated_at' => $todo->updated_at->format(DATE_ATOM),
            'tags' => $todo->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            }),
        ]);
    }

    /**
     * TODOを削除
     */
    public function destroy(Todo $todo): JsonResponse
    {
        $todo->tags()->detach();
        $todo->delete();

        return response()->json(null, 204);
    }

    /**
     * 完了済みTODO一覧を取得
     */
    public function completed(): JsonResponse
    {
        $todos = Todo::with(['difficulty', 'tags'])
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->get()
            ->map(function ($todo) {
                return [
                    'id' => $todo->id,
                    'difficulty_id' => $todo->difficulty_id,
                    'difficulty' => $todo->difficulty ? [
                        'id' => $todo->difficulty->id,
                        'name' => $todo->difficulty->name,
                        'points' => $todo->difficulty->points,
                        'color' => $todo->difficulty->color,
                    ] : null,
                    'content' => $todo->content,
                    'completed_at' => $todo->completed_at?->format(DATE_ATOM),
                    'created_at' => $todo->created_at->format(DATE_ATOM),
                    'updated_at' => $todo->updated_at->format(DATE_ATOM),
                    'tags' => $todo->tags->map(function ($tag) {
                        return [
                            'id' => $tag->id,
                            'name' => $tag->name,
                        ];
                    }),
                ];
            });

        return response()->json($todos);
    }
}

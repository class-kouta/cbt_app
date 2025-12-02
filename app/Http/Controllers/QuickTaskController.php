<?php

namespace App\Http\Controllers;

use App\Application\DTO\QuickTaskData;
use App\Application\UseCase\QuickTask\CreateQuickTaskUseCase;
use App\Application\UseCase\QuickTask\UpdateQuickTaskUseCase;
use App\Application\UseCase\QuickTask\DeleteQuickTaskUseCase;
use App\Http\Requests\QuickTask\CreateQuickTaskRequest;
use App\Http\Requests\QuickTask\UpdateQuickTaskRequest;
use App\Infrastructure\Database\Models\QuickTask;
use Illuminate\Http\JsonResponse;

class QuickTaskController extends Controller
{
    /**
     * クイックタスク一覧を取得（作成日時降順）
     */
    public function index(): JsonResponse
    {
        $quickTasks = QuickTask::with(['difficulty', 'tags'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($quickTask) {
                return [
                    'id' => $quickTask->id,
                    'content' => $quickTask->content,
                    'difficulty_id' => $quickTask->difficulty_id,
                    'difficulty' => $quickTask->difficulty ? [
                        'id' => $quickTask->difficulty->id,
                        'name' => $quickTask->difficulty->name,
                        'points' => $quickTask->difficulty->points,
                        'color' => $quickTask->difficulty->color,
                    ] : null,
                    'tags' => $quickTask->tags->map(function ($tag) {
                        return [
                            'id' => $tag->id,
                            'name' => $tag->name,
                        ];
                    }),
                    'created_at' => $quickTask->created_at->format(DATE_ATOM),
                    'updated_at' => $quickTask->updated_at->format(DATE_ATOM),
                ];
            });

        return response()->json($quickTasks);
    }

    /**
     * クイックタスクを作成
     */
    public function store(CreateQuickTaskRequest $request, CreateQuickTaskUseCase $createQuickTask): JsonResponse
    {
        $data = new QuickTaskData(
            content: (string) $request->string('content'),
            difficultyId: $request->has('difficulty_id') ? $request->integer('difficulty_id') : null,
            tagIds: (array) $request->input('tag_ids', [])
        );

        $quickTask = $createQuickTask->handle($data);

        return response()->json([
            'id' => $quickTask->getId(),
            'content' => $quickTask->getContent(),
            'difficulty_id' => $quickTask->getDifficultyId(),
            'tag_ids' => $quickTask->getTagIds(),
            'created_at' => $quickTask->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $quickTask->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * クイックタスクを更新
     */
    public function update(UpdateQuickTaskRequest $request, QuickTask $quickTask, UpdateQuickTaskUseCase $updateQuickTask): JsonResponse
    {
        $data = new QuickTaskData(
            content: (string) $request->string('content'),
            difficultyId: $request->has('difficulty_id') ? $request->integer('difficulty_id') : null,
            tagIds: (array) $request->input('tag_ids', [])
        );

        $updatedQuickTask = $updateQuickTask->handle($quickTask->id, $data);

        return response()->json([
            'id' => $updatedQuickTask->getId(),
            'content' => $updatedQuickTask->getContent(),
            'difficulty_id' => $updatedQuickTask->getDifficultyId(),
            'tag_ids' => $updatedQuickTask->getTagIds(),
            'created_at' => $updatedQuickTask->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedQuickTask->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * クイックタスクを削除
     */
    public function destroy(QuickTask $quickTask, DeleteQuickTaskUseCase $deleteQuickTask): JsonResponse
    {
        $deleteQuickTask->handle($quickTask->id);

        return response()->json(null, 204);
    }
}

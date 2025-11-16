<?php

namespace App\Http\Controllers;

use App\Application\DTO\TodoData;
use App\Application\UseCase\Todo\CreateTodoUseCase;
use App\Http\Requests\Todo\CreateTodoRequest;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
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
}

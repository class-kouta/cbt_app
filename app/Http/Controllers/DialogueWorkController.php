<?php

namespace App\Http\Controllers;

use App\Application\DTO\DialogueWorkData;
use App\Application\UseCase\DialogueWork\CreateDialogueWorkUseCase;
use App\Application\UseCase\DialogueWork\ListDialogueWorksUseCase;
use App\Application\UseCase\DialogueWork\UpdateDialogueWorkUseCase;
use App\Application\UseCase\DialogueWork\DeleteDialogueWorkUseCase;
use App\Domain\Repository\DialogueWorkRepositoryInterface;
use App\Http\Requests\DialogueWork\CreateDialogueWorkRequest;
use App\Http\Requests\DialogueWork\UpdateDialogueWorkRequest;
use App\Infrastructure\Database\Models\DialogueWork;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DialogueWorkController extends Controller
{
    public function index(ListDialogueWorksUseCase $listUseCase): JsonResponse
    {
        $items = $listUseCase->handle();

        return response()->json($items);
    }

    public function show(
        DialogueWork $dialogueWork,
        DialogueWorkRepositoryInterface $repository
    ): JsonResponse {
        $entity = $repository->findByIdForMember($dialogueWork->id, (int) Auth::id());

        if ($entity === null) {
            abort(404);
        }

        return response()->json([
            'id' => $entity->getId(),
            'content' => $entity->getContent(),
            'created_at' => $entity->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $entity->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    public function store(
        CreateDialogueWorkRequest $request,
        CreateDialogueWorkUseCase $createDialogueWork
    ): JsonResponse {
        $data = new DialogueWorkData(
            content: (string) $request->string('content')
        );

        $dialogueWork = $createDialogueWork->handle($data);

        return response()->json([
            'id' => $dialogueWork->getId(),
            'content' => $dialogueWork->getContent(),
            'created_at' => $dialogueWork->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $dialogueWork->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    public function update(
        UpdateDialogueWorkRequest $request,
        DialogueWork $dialogueWork,
        UpdateDialogueWorkUseCase $updateDialogueWork
    ): JsonResponse {
        $data = new DialogueWorkData(
            content: (string) $request->string('content')
        );

        $updatedDialogueWork = $updateDialogueWork->handle($dialogueWork->id, $data);

        return response()->json([
            'id' => $updatedDialogueWork->getId(),
            'content' => $updatedDialogueWork->getContent(),
            'created_at' => $updatedDialogueWork->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedDialogueWork->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    public function destroy(
        DialogueWork $dialogueWork,
        DeleteDialogueWorkUseCase $deleteDialogueWork
    ): JsonResponse {
        $deleteDialogueWork->handle($dialogueWork->id);

        return response()->json(null, 204);
    }
}

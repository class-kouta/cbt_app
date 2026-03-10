<?php

namespace App\Application\UseCase\DialogueWork;

use App\Domain\Repository\DialogueWorkRepositoryInterface;

class DeleteDialogueWorkUseCase
{
    public function __construct(private readonly DialogueWorkRepositoryInterface $dialogueWorkRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->dialogueWorkRepository->delete($id);
    }
}

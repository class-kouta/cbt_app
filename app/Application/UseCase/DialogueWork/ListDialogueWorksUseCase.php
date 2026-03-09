<?php

namespace App\Application\UseCase\DialogueWork;

use App\Domain\Repository\DialogueWorkRepositoryInterface;

class ListDialogueWorksUseCase
{
    public function __construct(
        private readonly DialogueWorkRepositoryInterface $dialogueWorkRepository
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(): array
    {
        return $this->dialogueWorkRepository->findAllOrderByCreatedAtDesc();
    }
}

<?php

namespace App\Application\UseCase\DialogueWork;

use App\Application\DTO\DialogueWorkData;
use App\Domain\Entity\DialogueWork as DialogueWorkEntity;
use App\Domain\Repository\DialogueWorkRepositoryInterface;

class CreateDialogueWorkUseCase
{
    public function __construct(private readonly DialogueWorkRepositoryInterface $dialogueWorkRepository)
    {
    }

    public function handle(DialogueWorkData $data): DialogueWorkEntity
    {
        $dialogueWork = DialogueWorkEntity::createNew($data->content);
        return $this->dialogueWorkRepository->save($dialogueWork);
    }
}

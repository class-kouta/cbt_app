<?php

namespace App\Application\UseCase\DialogueWork;

use App\Application\DTO\DialogueWorkData;
use App\Domain\Entity\DialogueWork as DialogueWorkEntity;
use App\Domain\Repository\DialogueWorkRepositoryInterface;
use DomainException;

class UpdateDialogueWorkUseCase
{
    public function __construct(private readonly DialogueWorkRepositoryInterface $dialogueWorkRepository)
    {
    }

    public function handle(int $id, DialogueWorkData $data): DialogueWorkEntity
    {
        $dialogueWork = $this->dialogueWorkRepository->findById($id);

        if ($dialogueWork === null) {
            throw new DomainException('Dialogue work not found.');
        }

        $updatedDialogueWork = $dialogueWork->update($data->content);
        return $this->dialogueWorkRepository->save($updatedDialogueWork);
    }
}

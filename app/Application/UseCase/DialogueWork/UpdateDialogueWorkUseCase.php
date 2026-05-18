<?php

namespace App\Application\UseCase\DialogueWork;

use App\Application\DTO\DialogueWorkData;
use App\Domain\Entity\DialogueWork as DialogueWorkEntity;
use App\Domain\Repository\DialogueWorkRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateDialogueWorkUseCase
{
    public function __construct(private readonly DialogueWorkRepositoryInterface $dialogueWorkRepository)
    {
    }

    public function handle(int $id, DialogueWorkData $data): DialogueWorkEntity
    {
        $memberId = (int) Auth::id();
        $dialogueWork = $this->dialogueWorkRepository->findByIdForMember($id, $memberId);

        if ($dialogueWork === null) {
            throw new DomainException('Dialogue work not found.');
        }

        $updatedDialogueWork = $dialogueWork->update($data->content);
        return $this->dialogueWorkRepository->saveForMember($updatedDialogueWork, $memberId);
    }
}

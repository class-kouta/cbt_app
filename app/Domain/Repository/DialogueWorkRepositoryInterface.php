<?php

namespace App\Domain\Repository;

use App\Domain\Entity\DialogueWork;

interface DialogueWorkRepositoryInterface
{
    public function saveForMember(DialogueWork $dialogueWork, int $memberId): DialogueWork;

    public function findByIdForMember(int $id, int $memberId): ?DialogueWork;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllForMemberOrderByCreatedAtDesc(int $memberId): array;

    public function deleteForMember(int $id, int $memberId): void;
}

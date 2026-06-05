<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SimpleNotepadTag as SimpleNotepadTagEntity;

interface SimpleNotepadTagRepositoryInterface
{
    /**
     * @return SimpleNotepadTagEntity[]
     */
    public function findAllForMember(int $memberId): array;

    /**
     * @return array<int, array{id: int, name: string, usage_count: int, created_at: string, updated_at: string}>
     */
    public function findAllSummariesForMember(int $memberId): array;

    public function countForMember(int $memberId): int;

    public function saveForMember(SimpleNotepadTagEntity $tag, int $memberId): SimpleNotepadTagEntity;

    public function findByIdForMember(int $id, int $memberId): ?SimpleNotepadTagEntity;

    public function deleteForMember(int $id, int $memberId): void;
}

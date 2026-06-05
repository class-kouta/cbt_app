<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SimpleNotepad;

interface SimpleNotepadRepositoryInterface
{
    public function findAllForMember(int $memberId): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllWithTagsForMember(int $memberId): array;

    public function saveForMember(SimpleNotepad $simpleNotepad, int $memberId): SimpleNotepad;

    /**
     * @param array<int> $tagIds
     * @return array<string, mixed>
     */
    public function saveWithTagsForMember(SimpleNotepad $simpleNotepad, array $tagIds, int $memberId): array;

    public function findByIdForMember(int $id, int $memberId): ?SimpleNotepad;

    public function deleteForMember(int $id, int $memberId): void;
}

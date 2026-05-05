<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SimpleNotepad;

interface SimpleNotepadRepositoryInterface
{
    public function findAllForMember(int $memberId): array;

    public function saveForMember(SimpleNotepad $simpleNotepad, int $memberId): SimpleNotepad;

    public function findByIdForMember(int $id, int $memberId): ?SimpleNotepad;

    public function deleteForMember(int $id, int $memberId): void;
}

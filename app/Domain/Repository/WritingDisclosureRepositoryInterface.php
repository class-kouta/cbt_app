<?php

namespace App\Domain\Repository;

use App\Domain\Entity\WritingDisclosure;

interface WritingDisclosureRepositoryInterface
{
    public function findAllForMember(int $memberId): array;

    public function saveForMember(WritingDisclosure $writingDisclosure, int $memberId): WritingDisclosure;

    public function findByIdForMember(int $id, int $memberId): ?WritingDisclosure;

    public function deleteForMember(int $id, int $memberId): void;
}

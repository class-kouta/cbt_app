<?php

namespace App\Domain\Repository;

use App\Domain\Entity\WritingDisclosure;

interface WritingDisclosureRepositoryInterface
{
    public function save(WritingDisclosure $writingDisclosure): WritingDisclosure;

    public function findById(int $id): ?WritingDisclosure;

    public function delete(int $id): void;
}

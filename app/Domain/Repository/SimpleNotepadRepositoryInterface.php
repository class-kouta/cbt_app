<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SimpleNotepad;

interface SimpleNotepadRepositoryInterface
{
    public function save(SimpleNotepad $simpleNotepad): SimpleNotepad;

    public function findById(int $id): ?SimpleNotepad;

    public function delete(int $id): void;
}

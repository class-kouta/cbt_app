<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Column;

interface ColumnRepositoryInterface
{
    public function save(Column $column): Column;

    public function findById(int $id): ?Column;

    public function delete(int $id): void;

    /**
     * @return Column[]
     */
    public function findAll(): array;
}

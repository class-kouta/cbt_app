<?php

namespace App\Domain\Repository;

use App\Domain\Entity\QuickTask;

interface QuickTaskRepositoryInterface
{
    public function save(QuickTask $quickTask): QuickTask;

    public function findById(int $id): ?QuickTask;

    /**
     * @return QuickTask[]
     */
    public function findAll(): array;

    public function delete(int $id): void;
}

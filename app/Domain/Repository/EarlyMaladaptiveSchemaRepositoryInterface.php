<?php

namespace App\Domain\Repository;

use App\Domain\Entity\EarlyMaladaptiveSchema;

interface EarlyMaladaptiveSchemaRepositoryInterface
{
    public function save(EarlyMaladaptiveSchema $schema): EarlyMaladaptiveSchema;

    public function findById(int $id): ?EarlyMaladaptiveSchema;

    public function findFirst(): ?EarlyMaladaptiveSchema;

    public function delete(int $id): void;
}

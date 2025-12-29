<?php

namespace App\Domain\Repository;

use App\Domain\Entity\StressorAndResponse;

interface StressorAndResponseRepositoryInterface
{
    public function save(StressorAndResponse $stressorAndResponse): StressorAndResponse;

    public function findById(int $id): ?StressorAndResponse;

    public function delete(int $id): void;

    /**
     * @return StressorAndResponse[]
     */
    public function findAll(): array;
}

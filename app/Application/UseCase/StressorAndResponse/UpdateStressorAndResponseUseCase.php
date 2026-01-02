<?php

namespace App\Application\UseCase\StressorAndResponse;

use App\Application\DTO\StressorAndResponseData;
use App\Domain\Entity\StressorAndResponse as StressorAndResponseEntity;
use App\Domain\Repository\StressorAndResponseRepositoryInterface;
use RuntimeException;

class UpdateStressorAndResponseUseCase
{
    public function __construct(private readonly StressorAndResponseRepositoryInterface $repository)
    {
    }

    public function handle(int $id, StressorAndResponseData $data): StressorAndResponseEntity
    {
        $existing = $this->repository->findById($id);

        if ($existing === null) {
            throw new RuntimeException('StressorAndResponse not found');
        }

        $updated = StressorAndResponseEntity::reconstitute(
            $id,
            $data->stressor,
            $data->cognition,
            $data->mood,
            $data->bodyReaction,
            $data->behavior,
            $data->stimulatedSchemas,
            $existing->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->save($updated);
    }
}

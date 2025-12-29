<?php

namespace App\Application\UseCase\StressorAndResponse;

use App\Application\DTO\StressorAndResponseData;
use App\Domain\Entity\StressorAndResponse as StressorAndResponseEntity;
use App\Domain\Repository\StressorAndResponseRepositoryInterface;

class CreateStressorAndResponseUseCase
{
    public function __construct(private readonly StressorAndResponseRepositoryInterface $repository)
    {
    }

    public function handle(StressorAndResponseData $data): StressorAndResponseEntity
    {
        $stressorAndResponse = StressorAndResponseEntity::createNew(
            $data->stressor,
            $data->cognition,
            $data->mood,
            $data->bodyReaction,
            $data->behavior
        );

        return $this->repository->save($stressorAndResponse);
    }
}

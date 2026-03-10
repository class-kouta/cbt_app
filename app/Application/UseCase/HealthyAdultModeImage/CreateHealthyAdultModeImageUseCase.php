<?php

namespace App\Application\UseCase\HealthyAdultModeImage;

use App\Application\DTO\HealthyAdultModeImageData;
use App\Domain\Entity\HealthyAdultModeImage as HealthyAdultModeImageEntity;
use App\Domain\Repository\HealthyAdultModeImageRepositoryInterface;

class CreateHealthyAdultModeImageUseCase
{
    public function __construct(private readonly HealthyAdultModeImageRepositoryInterface $repository)
    {
    }

    public function handle(HealthyAdultModeImageData $data): HealthyAdultModeImageEntity
    {
        $entity = HealthyAdultModeImageEntity::createNew($data->content);

        return $this->repository->save($entity);
    }
}

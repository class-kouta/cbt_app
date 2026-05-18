<?php

namespace App\Application\UseCase\HealthyAdultModeImage;

use App\Application\DTO\HealthyAdultModeImageData;
use App\Domain\Entity\HealthyAdultModeImage as HealthyAdultModeImageEntity;
use App\Domain\Repository\HealthyAdultModeImageRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateHealthyAdultModeImageUseCase
{
    public function __construct(private readonly HealthyAdultModeImageRepositoryInterface $repository)
    {
    }

    public function handle(HealthyAdultModeImageData $data): HealthyAdultModeImageEntity
    {
        $memberId = (int) Auth::id();
        $entity = HealthyAdultModeImageEntity::createNew($data->content);

        return $this->repository->saveForMember($entity, $memberId);
    }
}

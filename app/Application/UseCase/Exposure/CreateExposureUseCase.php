<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureData;
use App\Domain\Entity\Exposure as ExposureEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateExposureUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(ExposureData $data): ExposureEntity
    {
        $exposure = ExposureEntity::createNew(
            $data->avoidanceTarget,
            $data->exposureType,
            $data->selfTalk,
            $data->overallReflection,
            $data->nextGoal
        );

        return $this->repository->saveForMember($exposure, (int) Auth::id());
    }
}

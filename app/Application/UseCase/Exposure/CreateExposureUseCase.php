<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureData;
use App\Domain\Entity\Exposure as ExposureEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateExposureUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(ExposureData $data): ExposureEntity
    {
        return DB::transaction(function () use ($data) {
            $memberId = (int) Auth::id();

            $exposure = ExposureEntity::createNew(
                $data->avoidanceTarget,
                $data->exposureType,
                $data->selfTalk,
                $data->overallReflection,
                $data->nextGoal
            );

            $saved = $this->repository->saveForMember($exposure, $memberId);
            $this->repository->syncTagsForMember($saved->getId(), $data->tagIds, $memberId);

            return $saved;
        });
    }
}

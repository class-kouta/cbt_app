<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureData;
use App\Domain\Entity\Exposure as ExposureEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateExposureUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $id, ExposureData $data): ExposureEntity
    {
        $existing = $this->repository->findByIdForMember($id, (int) Auth::id());

        if ($existing === null) {
            throw new \RuntimeException('Exposure not found');
        }

        $updated = $existing->update(
            $data->avoidanceTarget,
            $data->exposureType,
            $data->selfTalk,
            $data->overallReflection,
            $data->nextGoal
        );

        return $this->repository->saveForMember($updated, (int) Auth::id());
    }
}

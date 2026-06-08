<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureData;
use App\Domain\Entity\Exposure as ExposureEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateExposureUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $id, ExposureData $data): ExposureEntity
    {
        return DB::transaction(function () use ($id, $data) {
            $memberId = (int) Auth::id();
            $existing = $this->repository->findByIdForMember($id, $memberId);

            if ($existing === null) {
                throw new \RuntimeException('Exposure not found');
            }

            $updated = $existing->update(
                $data->avoidanceTarget,
                null,
                $data->selfTalk,
                $data->overallReflection,
                $data->nextGoal
            );

            return $this->repository->saveForMember($updated, $memberId);
        });
    }
}

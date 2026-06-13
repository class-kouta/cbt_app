<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureSessionData;
use App\Domain\Entity\ExposureSession as ExposureSessionEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AddSessionUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $exposureId, ExposureSessionData $data): ExposureSessionEntity
    {
        $exposure = $this->repository->findByIdForMember($exposureId, (int) Auth::id());

        if ($exposure === null) {
            throw new \RuntimeException('Exposure not found');
        }

        if (! $exposure->canAddNewSession()) {
            throw new \InvalidArgumentException('前回の実施記録の振り返りを完了してから、新しい記録を追加してください');
        }

        $memberId = (int) Auth::id();

        if ($data->hierarchyItemId !== null
            && ! $this->repository->hierarchyItemBelongsToExposureForMember($data->hierarchyItemId, $exposureId, $memberId)) {
            throw new \InvalidArgumentException('Invalid hierarchy item');
        }

        $session = ExposureSessionEntity::createNew(
            0,
            $data->hierarchyItemId,
            $data->sudsAfter,
            $data->reflection
        );

        return $this->repository->saveSessionForMember($exposureId, $session, (int) Auth::id());
    }
}

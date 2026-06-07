<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureSessionData;
use App\Domain\Entity\ExposureSession as ExposureSessionEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use DateTimeImmutable;
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

        $memberId = (int) Auth::id();

        if ($data->hierarchyItemId !== null
            && ! $this->repository->hierarchyItemBelongsToExposureForMember($data->hierarchyItemId, $exposureId, $memberId)) {
            throw new \InvalidArgumentException('Invalid hierarchy item');
        }

        $latestSession = $exposure->getLatestSession();
        $nextSessionNumber = $latestSession ? $latestSession->getSessionNumber() + 1 : 1;

        $performedAt = $data->performedAt
            ? new DateTimeImmutable($data->performedAt)
            : null;

        $session = ExposureSessionEntity::createNew(
            $nextSessionNumber,
            $data->hierarchyItemId,
            $data->actionPlan,
            $data->sudsBefore,
            $data->sudsPeak,
            $data->sudsAfter,
            $performedAt,
            $data->reflection
        );

        return $this->repository->saveSessionForMember($exposureId, $session, (int) Auth::id());
    }
}

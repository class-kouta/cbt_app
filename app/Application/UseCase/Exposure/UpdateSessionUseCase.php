<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureSessionData;
use App\Domain\Entity\ExposureSession as ExposureSessionEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateSessionUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $sessionId, ExposureSessionData $data): ExposureSessionEntity
    {
        $existing = $this->repository->findSessionByIdForMember($sessionId, (int) Auth::id());

        if ($existing === null) {
            throw new \RuntimeException('Session not found');
        }

        $memberId = (int) Auth::id();

        if ($data->hierarchyItemId !== null
            && ! $this->repository->hierarchyItemBelongsToExposureForMember(
                $data->hierarchyItemId,
                (int) $existing->getExposureId(),
                $memberId
            )) {
            throw new \InvalidArgumentException('Invalid hierarchy item');
        }

        $updated = $existing->update(
            $data->hierarchyItemId,
            $data->sudsAfter,
            $data->reflection
        );

        return $this->repository->updateSessionForMember($updated, (int) Auth::id());
    }
}

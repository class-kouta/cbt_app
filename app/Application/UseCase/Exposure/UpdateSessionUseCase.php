<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureSessionData;
use App\Domain\Entity\ExposureSession as ExposureSessionEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use DateTimeImmutable;
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

        $performedAt = $data->performedAt
            ? new DateTimeImmutable($data->performedAt)
            : null;

        $updated = $existing->update(
            $data->hierarchyItemId,
            $data->actionPlan,
            $data->sudsBefore,
            $data->sudsPeak,
            $data->sudsAfter,
            $performedAt,
            $data->reflection
        );

        return $this->repository->updateSessionForMember($updated, (int) Auth::id());
    }
}

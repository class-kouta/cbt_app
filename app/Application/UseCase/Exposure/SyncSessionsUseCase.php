<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureSessionBulkItemData;
use App\Domain\Entity\ExposureSession as ExposureSessionEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\Auth;

class SyncSessionsUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    /**
     * @param ExposureSessionBulkItemData[] $itemsData
     * @return ExposureSessionEntity[]
     */
    public function handle(int $exposureId, array $itemsData): array
    {
        $memberId = (int) Auth::id();
        $exposure = $this->repository->findByIdForMember($exposureId, $memberId);

        if ($exposure === null) {
            throw new \RuntimeException('Exposure not found');
        }

        $sessions = [];
        $sessionNumber = 1;

        foreach ($itemsData as $itemData) {
            if ($itemData->id === null && ! $itemData->hasContent()) {
                continue;
            }

            if ($itemData->hierarchyItemId !== null
                && ! $this->repository->hierarchyItemBelongsToExposureForMember($itemData->hierarchyItemId, $exposureId, $memberId)) {
                throw new \InvalidArgumentException('Invalid hierarchy item');
            }

            $performedAt = $itemData->performedAt
                ? new DateTimeImmutable($itemData->performedAt)
                : null;

            if ($itemData->id !== null) {
                $existing = $this->repository->findSessionByIdForMember($itemData->id, $memberId);
                if ($existing === null || $existing->getExposureId() !== $exposureId) {
                    throw new \InvalidArgumentException('Invalid session');
                }

                $sessions[] = $existing->update(
                    $itemData->hierarchyItemId,
                    $itemData->actionPlan,
                    $itemData->sudsBefore,
                    $itemData->sudsPeak,
                    $itemData->sudsAfter,
                    $performedAt,
                    $itemData->reflection
                );
            } else {
                $sessions[] = ExposureSessionEntity::createNew(
                    $sessionNumber,
                    $itemData->hierarchyItemId,
                    $itemData->actionPlan,
                    $itemData->sudsBefore,
                    $itemData->sudsPeak,
                    $itemData->sudsAfter,
                    $performedAt,
                    $itemData->reflection
                );
            }

            $sessionNumber++;
        }

        return $this->repository->syncSessionsForMember($exposureId, $sessions, $memberId);
    }
}

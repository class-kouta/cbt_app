<?php

namespace App\Application\UseCase\StressorAndResponse;

use App\Application\DTO\StressorAndResponseData;
use App\Domain\Entity\StressorAndResponse as StressorAndResponseEntity;
use App\Domain\Repository\StressorAndResponseRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class UpdateStressorAndResponseUseCase
{
    public function __construct(private readonly StressorAndResponseRepositoryInterface $repository)
    {
    }

    public function handle(int $id, StressorAndResponseData $data): StressorAndResponseEntity
    {
        $memberId = (int) Auth::id();
        $existing = $this->repository->findByIdForMember($id, $memberId);

        if ($existing === null) {
            throw new RuntimeException('StressorAndResponse not found');
        }

        $updated = StressorAndResponseEntity::reconstitute(
            $id,
            $data->stressor,
            $data->cognition,
            $data->mood,
            $data->bodyReaction,
            $data->behavior,
            $existing->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->saveForMember($updated, $memberId);
    }
}

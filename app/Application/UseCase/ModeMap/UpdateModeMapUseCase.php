<?php

namespace App\Application\UseCase\ModeMap;

use App\Application\DTO\ModeMapData;
use App\Domain\Entity\ModeMap as ModeMapEntity;
use App\Domain\Repository\ModeMapRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateModeMapUseCase
{
    public function __construct(private readonly ModeMapRepositoryInterface $repository) {}

    public function handle(int $id, ModeMapData $data): ModeMapEntity
    {
        $memberId = (int) Auth::id();
        $existingModeMap = $this->repository->findByIdForMember($id, $memberId);

        if ($existingModeMap === null) {
            throw new \RuntimeException('モードマップが見つかりません');
        }

        $modeMap = ModeMapEntity::reconstitute(
            $id,
            $data->woundedChildMode,
            $data->hurtfulAdultMode,
            $data->unacceptableCopingMode,
            $data->healthyHappyChildMode,
            $data->healthyAdultMode,
            $existingModeMap->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->saveForMember($modeMap, $memberId);
    }
}

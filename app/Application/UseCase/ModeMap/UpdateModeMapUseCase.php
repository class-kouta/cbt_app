<?php

namespace App\Application\UseCase\ModeMap;

use App\Application\DTO\ModeMapData;
use App\Domain\Entity\ModeMap as ModeMapEntity;
use App\Domain\Repository\ModeMapRepositoryInterface;

class UpdateModeMapUseCase
{
    public function __construct(private readonly ModeMapRepositoryInterface $repository) {}

    public function handle(int $id, ModeMapData $data): ModeMapEntity
    {
        $existingModeMap = $this->repository->findById($id);

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

        return $this->repository->save($modeMap);
    }
}

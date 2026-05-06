<?php

namespace App\Application\UseCase\ModeMap;

use App\Application\DTO\ModeMapData;
use App\Domain\Entity\ModeMap as ModeMapEntity;
use App\Domain\Repository\ModeMapRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateModeMapUseCase
{
    public function __construct(private readonly ModeMapRepositoryInterface $repository) {}

    public function handle(ModeMapData $data): ModeMapEntity
    {
        $modeMap = ModeMapEntity::createNew(
            $data->woundedChildMode,
            $data->hurtfulAdultMode,
            $data->unacceptableCopingMode,
            $data->healthyHappyChildMode,
            $data->healthyAdultMode
        );

        return $this->repository->saveForMember($modeMap, (int) Auth::id());
    }
}

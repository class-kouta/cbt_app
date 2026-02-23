<?php

namespace App\Application\UseCase\SafePlace;

use App\Application\DTO\SafePlaceData;
use App\Domain\Entity\SafePlace as SafePlaceEntity;
use App\Domain\Repository\SafePlaceRepositoryInterface;

class CreateSafePlaceUseCase
{
    public function __construct(private readonly SafePlaceRepositoryInterface $repository)
    {
    }

    public function handle(SafePlaceData $data): SafePlaceEntity
    {
        $safePlace = SafePlaceEntity::createNew(
            $data->safeImage,
            $data->safeSomething
        );

        return $this->repository->save($safePlace);
    }
}

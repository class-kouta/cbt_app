<?php

namespace App\Application\UseCase\SafePlace;

use App\Application\DTO\SafePlaceData;
use App\Domain\Entity\SafePlace as SafePlaceEntity;
use App\Domain\Repository\SafePlaceRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateSafePlaceUseCase
{
    public function __construct(private readonly SafePlaceRepositoryInterface $repository)
    {
    }

    public function handle(SafePlaceData $data): SafePlaceEntity
    {
        $memberId = (int) Auth::id();
        $safePlace = SafePlaceEntity::createNew(
            $data->safeImage,
            $data->safeSomething
        );

        return $this->repository->saveForMember($safePlace, $memberId);
    }
}

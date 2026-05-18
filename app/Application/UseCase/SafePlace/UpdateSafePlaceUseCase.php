<?php

namespace App\Application\UseCase\SafePlace;

use App\Application\DTO\SafePlaceData;
use App\Domain\Entity\SafePlace as SafePlaceEntity;
use App\Domain\Repository\SafePlaceRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateSafePlaceUseCase
{
    public function __construct(private readonly SafePlaceRepositoryInterface $repository)
    {
    }

    public function handle(int $id, SafePlaceData $data): SafePlaceEntity
    {
        $memberId = (int) Auth::id();
        $existingSafePlace = $this->repository->findByIdForMember($id, $memberId);

        if ($existingSafePlace === null) {
            throw new \RuntimeException('安全なイメージと安全な何かが見つかりません');
        }

        $safePlace = SafePlaceEntity::reconstitute(
            $id,
            $data->safeImage,
            $data->safeSomething,
            $existingSafePlace->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->saveForMember($safePlace, $memberId);
    }
}

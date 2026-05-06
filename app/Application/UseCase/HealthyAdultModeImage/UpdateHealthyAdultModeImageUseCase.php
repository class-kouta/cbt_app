<?php

namespace App\Application\UseCase\HealthyAdultModeImage;

use App\Application\DTO\HealthyAdultModeImageData;
use App\Domain\Entity\HealthyAdultModeImage as HealthyAdultModeImageEntity;
use App\Domain\Repository\HealthyAdultModeImageRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateHealthyAdultModeImageUseCase
{
    public function __construct(private readonly HealthyAdultModeImageRepositoryInterface $repository)
    {
    }

    public function handle(int $id, HealthyAdultModeImageData $data): HealthyAdultModeImageEntity
    {
        $memberId = (int) Auth::id();
        $existing = $this->repository->findByIdForMember($id, $memberId);

        if ($existing === null) {
            throw new \RuntimeException('ヘルシーな大人モードのイメージが見つかりません');
        }

        $entity = HealthyAdultModeImageEntity::reconstitute(
            $id,
            $data->content,
            $existing->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->saveForMember($entity, $memberId);
    }
}

<?php

namespace App\Application\UseCase\HealthyAdultModeImage;

use App\Application\DTO\HealthyAdultModeImageData;
use App\Domain\Entity\HealthyAdultModeImage as HealthyAdultModeImageEntity;
use App\Domain\Repository\HealthyAdultModeImageRepositoryInterface;

class UpdateHealthyAdultModeImageUseCase
{
    public function __construct(private readonly HealthyAdultModeImageRepositoryInterface $repository)
    {
    }

    public function handle(int $id, HealthyAdultModeImageData $data): HealthyAdultModeImageEntity
    {
        $existing = $this->repository->findById($id);

        if ($existing === null) {
            throw new \RuntimeException('ヘルシーな大人モードのイメージが見つかりません');
        }

        $entity = HealthyAdultModeImageEntity::reconstitute(
            $id,
            $data->content,
            $existing->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->save($entity);
    }
}

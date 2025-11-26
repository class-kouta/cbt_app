<?php

namespace App\Application\UseCase\Coping;

use App\Application\DTO\CopingData;
use App\Domain\Entity\Coping as CopingEntity;
use App\Domain\Repository\CopingRepositoryInterface;
use DomainException;

class UpdateCopingUseCase
{
    public function __construct(private readonly CopingRepositoryInterface $copingRepository)
    {
    }

    public function handle(int $id, CopingData $data): CopingEntity
    {
        $coping = $this->copingRepository->findById($id);

        if ($coping === null) {
            throw new DomainException('Coping not found.');
        }

        $updatedCoping = $coping->updateContent($data->content, $data->copingTagIds, $data->point);
        return $this->copingRepository->save($updatedCoping);
    }
}

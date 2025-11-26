<?php

namespace App\Application\UseCase\Coping;

use App\Application\DTO\CopingData;
use App\Domain\Entity\Coping as CopingEntity;
use App\Domain\Repository\CopingRepositoryInterface;

class CreateCopingUseCase
{
    public function __construct(private readonly CopingRepositoryInterface $copingRepository)
    {
    }

    public function handle(CopingData $data): CopingEntity
    {
        $coping = CopingEntity::createNew($data->content, $data->copingTagIds);
        return $this->copingRepository->save($coping);
    }
}

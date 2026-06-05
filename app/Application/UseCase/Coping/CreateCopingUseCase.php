<?php

namespace App\Application\UseCase\Coping;

use App\Application\DTO\CopingData;
use App\Domain\Entity\Coping as CopingEntity;
use App\Domain\Repository\CopingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateCopingUseCase
{
    public function __construct(private readonly CopingRepositoryInterface $copingRepository)
    {
    }

    public function handle(CopingData $data): CopingEntity
    {
        $memberId = (int) Auth::id();
        $coping = CopingEntity::createNew($data->content);

        return $this->copingRepository->saveForMember($coping, $memberId);
    }
}

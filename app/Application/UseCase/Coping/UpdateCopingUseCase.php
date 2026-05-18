<?php

namespace App\Application\UseCase\Coping;

use App\Application\DTO\CopingData;
use App\Domain\Entity\Coping as CopingEntity;
use App\Domain\Repository\CopingRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateCopingUseCase
{
    public function __construct(private readonly CopingRepositoryInterface $copingRepository)
    {
    }

    public function handle(int $id, CopingData $data): CopingEntity
    {
        $memberId = (int) Auth::id();
        $coping = $this->copingRepository->findByIdForMember($id, $memberId);

        if ($coping === null) {
            throw new DomainException('Coping not found.');
        }

        $updatedCoping = $coping->updateContent($data->content, $data->copingTagIds, $data->point);

        return $this->copingRepository->saveForMember($updatedCoping, $memberId);
    }
}

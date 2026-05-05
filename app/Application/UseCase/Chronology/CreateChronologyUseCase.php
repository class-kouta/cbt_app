<?php

namespace App\Application\UseCase\Chronology;

use App\Application\DTO\ChronologyData;
use App\Domain\Entity\Chronology as ChronologyEntity;
use App\Domain\Repository\ChronologyRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateChronologyUseCase
{
    public function __construct(private readonly ChronologyRepositoryInterface $chronologyRepository) {}

    public function handle(ChronologyData $data): ChronologyEntity
    {
        $memberId = Auth::id();

        $chronology = ChronologyEntity::createNew(
            $data->whenPeriod,
            $data->environmentEvent,
            $data->experienceFeeling,
            $data->sentimentType
        );

        return $this->chronologyRepository->saveForMember($chronology, $memberId);
    }
}

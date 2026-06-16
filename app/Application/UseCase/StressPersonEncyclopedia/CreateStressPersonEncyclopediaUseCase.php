<?php

namespace App\Application\UseCase\StressPersonEncyclopedia;

use App\Application\DTO\StressPersonEncyclopediaData;
use App\Domain\Entity\StressPersonEncyclopedia;
use App\Domain\Repository\StressPersonEncyclopediaRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateStressPersonEncyclopediaUseCase
{
    public function __construct(
        private readonly StressPersonEncyclopediaRepositoryInterface $stressPersonEncyclopediaRepository,
    ) {
    }

    public function handle(StressPersonEncyclopediaData $data): StressPersonEncyclopedia
    {
        $now = now()->toDateTimeImmutable();

        $encyclopedia = StressPersonEncyclopedia::createNew(
            name: $data->name,
            relationship: $data->relationship,
            difficultTraits: $data->difficultTraits,
            myReaction: $data->myReaction,
            copingStrategy: $data->copingStrategy,
            notes: $data->notes,
            createdAt: $now,
        );

        return $this->stressPersonEncyclopediaRepository->saveForMember($encyclopedia, (int) Auth::id());
    }
}

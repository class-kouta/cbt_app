<?php

namespace App\Application\UseCase\StressPersonEncyclopedia;

use App\Application\DTO\StressPersonEncyclopediaData;
use App\Domain\Entity\StressPersonEncyclopedia;
use App\Domain\Repository\StressPersonEncyclopediaRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateStressPersonEncyclopediaUseCase
{
    public function __construct(
        private readonly StressPersonEncyclopediaRepositoryInterface $stressPersonEncyclopediaRepository,
    ) {
    }

    public function handle(int $id, StressPersonEncyclopediaData $data): StressPersonEncyclopedia
    {
        $memberId = (int) Auth::id();
        $encyclopedia = $this->stressPersonEncyclopediaRepository->findByIdForMember($id, $memberId);

        if ($encyclopedia === null) {
            throw new DomainException('Stress person encyclopedia not found.');
        }

        $updated = $encyclopedia->update(
            name: $data->name,
            relationship: $data->relationship,
            difficultTraits: $data->difficultTraits,
            myReaction: $data->myReaction,
            copingStrategy: $data->copingStrategy,
            notes: $data->notes,
            updatedAt: now()->toDateTimeImmutable(),
        );

        return $this->stressPersonEncyclopediaRepository->saveForMember($updated, $memberId);
    }
}

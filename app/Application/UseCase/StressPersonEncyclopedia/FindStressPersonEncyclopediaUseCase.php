<?php

namespace App\Application\UseCase\StressPersonEncyclopedia;

use App\Domain\Entity\StressPersonEncyclopedia;
use App\Domain\Repository\StressPersonEncyclopediaRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class FindStressPersonEncyclopediaUseCase
{
    public function __construct(
        private readonly StressPersonEncyclopediaRepositoryInterface $stressPersonEncyclopediaRepository,
    ) {
    }

    public function handle(int $id): StressPersonEncyclopedia
    {
        $encyclopedia = $this->stressPersonEncyclopediaRepository->findByIdForMember($id, (int) Auth::id());

        if ($encyclopedia === null) {
            throw new DomainException('Stress person encyclopedia not found.');
        }

        return $encyclopedia;
    }
}

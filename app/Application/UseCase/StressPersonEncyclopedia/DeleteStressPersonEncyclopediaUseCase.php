<?php

namespace App\Application\UseCase\StressPersonEncyclopedia;

use App\Domain\Repository\StressPersonEncyclopediaRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class DeleteStressPersonEncyclopediaUseCase
{
    public function __construct(
        private readonly StressPersonEncyclopediaRepositoryInterface $stressPersonEncyclopediaRepository,
    ) {
    }

    public function handle(int $id): void
    {
        $memberId = (int) Auth::id();
        $encyclopedia = $this->stressPersonEncyclopediaRepository->findByIdForMember($id, $memberId);

        if ($encyclopedia === null) {
            throw new DomainException('Stress person encyclopedia not found.');
        }

        $this->stressPersonEncyclopediaRepository->deleteForMember($id, $memberId);
    }
}

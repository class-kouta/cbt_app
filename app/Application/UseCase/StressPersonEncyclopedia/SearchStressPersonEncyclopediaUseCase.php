<?php

namespace App\Application\UseCase\StressPersonEncyclopedia;

use App\Domain\Entity\StressPersonEncyclopedia;
use App\Domain\Repository\StressPersonEncyclopediaRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchStressPersonEncyclopediaUseCase
{
    public function __construct(
        private readonly StressPersonEncyclopediaRepositoryInterface $stressPersonEncyclopediaRepository,
        private readonly PresentStressPersonEncyclopediaUseCase $presentStressPersonEncyclopedia,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function handle(): array
    {
        $encyclopedias = $this->stressPersonEncyclopediaRepository->findAllForMember((int) Auth::id());

        return array_map(
            fn (StressPersonEncyclopedia $encyclopedia) => $this->presentStressPersonEncyclopedia->handle($encyclopedia),
            $encyclopedias,
        );
    }
}

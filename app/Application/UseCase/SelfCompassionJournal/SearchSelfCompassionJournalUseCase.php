<?php

namespace App\Application\UseCase\SelfCompassionJournal;

use App\Domain\Entity\SelfCompassionJournal;
use App\Domain\Repository\SelfCompassionJournalRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchSelfCompassionJournalUseCase
{
    public function __construct(
        private readonly SelfCompassionJournalRepositoryInterface $selfCompassionJournalRepository,
        private readonly PresentSelfCompassionJournalUseCase $presentSelfCompassionJournal,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function handle(): array
    {
        $journals = $this->selfCompassionJournalRepository->findAllForMember((int) Auth::id());

        return array_map(
            fn (SelfCompassionJournal $journal) => $this->presentSelfCompassionJournal->handle($journal),
            $journals,
        );
    }
}

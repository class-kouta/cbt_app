<?php

namespace App\Application\UseCase\SelfCompassionJournal;

use App\Domain\Entity\SelfCompassionJournal;
use App\Domain\Repository\SelfCompassionJournalRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class FindSelfCompassionJournalUseCase
{
    public function __construct(
        private readonly SelfCompassionJournalRepositoryInterface $selfCompassionJournalRepository,
    ) {
    }

    public function handle(int $id): SelfCompassionJournal
    {
        $journal = $this->selfCompassionJournalRepository->findByIdForMember($id, (int) Auth::id());

        if ($journal === null) {
            throw new DomainException('Self compassion journal not found.');
        }

        return $journal;
    }
}

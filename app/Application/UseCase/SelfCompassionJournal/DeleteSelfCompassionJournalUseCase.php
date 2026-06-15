<?php

namespace App\Application\UseCase\SelfCompassionJournal;

use App\Domain\Repository\SelfCompassionJournalRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class DeleteSelfCompassionJournalUseCase
{
    public function __construct(
        private readonly SelfCompassionJournalRepositoryInterface $selfCompassionJournalRepository,
    ) {
    }

    public function handle(int $id): void
    {
        $memberId = (int) Auth::id();
        $journal = $this->selfCompassionJournalRepository->findByIdForMember($id, $memberId);

        if ($journal === null) {
            throw new DomainException('Self compassion journal not found.');
        }

        $this->selfCompassionJournalRepository->deleteForMember($id, $memberId);
    }
}

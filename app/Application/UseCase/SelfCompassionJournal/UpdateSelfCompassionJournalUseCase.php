<?php

namespace App\Application\UseCase\SelfCompassionJournal;

use App\Application\DTO\SelfCompassionJournalData;
use App\Domain\Entity\SelfCompassionJournal;
use App\Domain\Repository\SelfCompassionJournalRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateSelfCompassionJournalUseCase
{
    public function __construct(
        private readonly SelfCompassionJournalRepositoryInterface $selfCompassionJournalRepository,
    ) {
    }

    public function handle(int $id, SelfCompassionJournalData $data): SelfCompassionJournal
    {
        $memberId = (int) Auth::id();
        $journal = $this->selfCompassionJournalRepository->findByIdForMember($id, $memberId);

        if ($journal === null) {
            throw new DomainException('Self compassion journal not found.');
        }

        $updated = $journal->update(
            difficultExperience: $data->difficultExperience,
            effortMade: $data->effortMade,
            friendVoice: $data->friendVoice,
            wordToSelf: $data->wordToSelf,
            updatedAt: now()->toDateTimeImmutable(),
        );

        return $this->selfCompassionJournalRepository->saveForMember($updated, $memberId);
    }
}

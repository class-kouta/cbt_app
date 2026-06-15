<?php

namespace App\Application\UseCase\SelfCompassionJournal;

use App\Application\DTO\SelfCompassionJournalData;
use App\Domain\Entity\SelfCompassionJournal;
use App\Domain\Repository\SelfCompassionJournalRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateSelfCompassionJournalUseCase
{
    public function __construct(
        private readonly SelfCompassionJournalRepositoryInterface $selfCompassionJournalRepository,
    ) {
    }

    public function handle(SelfCompassionJournalData $data): SelfCompassionJournal
    {
        $now = now()->toDateTimeImmutable();

        $journal = SelfCompassionJournal::createNew(
            difficultExperience: $data->difficultExperience,
            effortMade: $data->effortMade,
            friendVoice: $data->friendVoice,
            wordToSelf: $data->wordToSelf,
            createdAt: $now,
        );

        return $this->selfCompassionJournalRepository->saveForMember($journal, (int) Auth::id());
    }
}

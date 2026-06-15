<?php

namespace App\Application\UseCase\SelfCompassionJournal;

use App\Domain\Entity\SelfCompassionJournal;

class PresentSelfCompassionJournalUseCase
{
    /**
     * @return array<string, mixed>
     */
    public function handle(SelfCompassionJournal $journal): array
    {
        return [
            'id' => $journal->getId(),
            'difficult_experience' => $journal->getDifficultExperience(),
            'effort_made' => $journal->getEffortMade(),
            'friend_voice' => $journal->getFriendVoice(),
            'word_to_self' => $journal->getWordToSelf(),
            'created_at' => $journal->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $journal->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}

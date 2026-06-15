<?php

namespace App\Application\DTO;

class SelfCompassionJournalData
{
    public function __construct(
        public readonly string $difficultExperience,
        public readonly string $effortMade,
        public readonly string $friendVoice,
        public readonly string $wordToSelf,
    ) {
    }
}

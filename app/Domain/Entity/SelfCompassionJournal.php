<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class SelfCompassionJournal
{
    private function __construct(
        private ?int $id,
        private string $difficultExperience,
        private string $effortMade,
        private string $friendVoice,
        private string $wordToSelf,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    public static function createNew(
        string $difficultExperience,
        string $effortMade,
        string $friendVoice,
        string $wordToSelf,
        DateTimeImmutable $createdAt,
    ): self {
        return new self(
            null,
            $difficultExperience,
            $effortMade,
            $friendVoice,
            $wordToSelf,
            $createdAt,
            $createdAt,
        );
    }

    public static function reconstitute(
        int $id,
        string $difficultExperience,
        string $effortMade,
        string $friendVoice,
        string $wordToSelf,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            $id,
            $difficultExperience,
            $effortMade,
            $friendVoice,
            $wordToSelf,
            $createdAt,
            $updatedAt,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDifficultExperience(): string
    {
        return $this->difficultExperience;
    }

    public function getEffortMade(): string
    {
        return $this->effortMade;
    }

    public function getFriendVoice(): string
    {
        return $this->friendVoice;
    }

    public function getWordToSelf(): string
    {
        return $this->wordToSelf;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->difficultExperience,
            $this->effortMade,
            $this->friendVoice,
            $this->wordToSelf,
            $this->createdAt,
            $this->updatedAt,
        );
    }
}

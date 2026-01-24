<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class AnxietyDiary
{
    private ?int $id;
    private string $situation;
    private ?string $anxietyThought;
    private ?string $actualOutcome;
    private ?int $stressorAndResponseId;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        string $situation,
        ?string $anxietyThought,
        ?string $actualOutcome,
        ?int $stressorAndResponseId,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->situation = $situation;
        $this->anxietyThought = $anxietyThought;
        $this->actualOutcome = $actualOutcome;
        $this->stressorAndResponseId = $stressorAndResponseId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        string $situation,
        ?string $anxietyThought,
        ?string $actualOutcome,
        ?int $stressorAndResponseId = null
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            $situation,
            $anxietyThought,
            $actualOutcome,
            $stressorAndResponseId,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        string $situation,
        ?string $anxietyThought,
        ?string $actualOutcome,
        ?int $stressorAndResponseId,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $situation,
            $anxietyThought,
            $actualOutcome,
            $stressorAndResponseId,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSituation(): string
    {
        return $this->situation;
    }

    public function getAnxietyThought(): ?string
    {
        return $this->anxietyThought;
    }

    public function getActualOutcome(): ?string
    {
        return $this->actualOutcome;
    }

    public function getStressorAndResponseId(): ?int
    {
        return $this->stressorAndResponseId;
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
            $this->situation,
            $this->anxietyThought,
            $this->actualOutcome,
            $this->stressorAndResponseId,
            $this->createdAt,
            $this->updatedAt
        );
    }
}

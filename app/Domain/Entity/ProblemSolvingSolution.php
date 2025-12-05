<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class ProblemSolvingSolution
{
    private ?int $id;
    private ?int $problemSolvingId;
    private string $content;
    private ?int $effectiveness;
    private ?int $feasibility;
    private int $sortOrder;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ?int $problemSolvingId,
        string $content,
        ?int $effectiveness,
        ?int $feasibility,
        int $sortOrder,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->problemSolvingId = $problemSolvingId;
        $this->content = $content;
        $this->effectiveness = $effectiveness;
        $this->feasibility = $feasibility;
        $this->sortOrder = $sortOrder;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        string $content,
        int $sortOrder,
        ?int $effectiveness = null,
        ?int $feasibility = null
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            null,
            $content,
            $effectiveness,
            $feasibility,
            $sortOrder,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        int $problemSolvingId,
        string $content,
        ?int $effectiveness,
        ?int $feasibility,
        int $sortOrder,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $problemSolvingId,
            $content,
            $effectiveness,
            $feasibility,
            $sortOrder,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProblemSolvingId(): ?int
    {
        return $this->problemSolvingId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getEffectiveness(): ?int
    {
        return $this->effectiveness;
    }

    public function getFeasibility(): ?int
    {
        return $this->feasibility;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
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
            $this->problemSolvingId,
            $this->content,
            $this->effectiveness,
            $this->feasibility,
            $this->sortOrder,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function withProblemSolvingId(int $problemSolvingId): self
    {
        return new self(
            $this->id,
            $problemSolvingId,
            $this->content,
            $this->effectiveness,
            $this->feasibility,
            $this->sortOrder,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function update(
        string $content,
        ?int $effectiveness,
        ?int $feasibility
    ): self {
        return new self(
            $this->id,
            $this->problemSolvingId,
            $content,
            $effectiveness,
            $feasibility,
            $this->sortOrder,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}

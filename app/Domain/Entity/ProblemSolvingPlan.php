<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class ProblemSolvingPlan
{
    private ?int $id;
    private ?int $problemSolvingId;
    private int $planNumber;
    private ?string $actionPlan;
    private ?string $reflection;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ?int $problemSolvingId,
        int $planNumber,
        ?string $actionPlan,
        ?string $reflection,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->problemSolvingId = $problemSolvingId;
        $this->planNumber = $planNumber;
        $this->actionPlan = $actionPlan;
        $this->reflection = $reflection;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        int $planNumber,
        ?string $actionPlan = null,
        ?string $reflection = null
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            null,
            $planNumber,
            $actionPlan,
            $reflection,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        int $problemSolvingId,
        int $planNumber,
        ?string $actionPlan,
        ?string $reflection,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $problemSolvingId,
            $planNumber,
            $actionPlan,
            $reflection,
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

    public function getPlanNumber(): int
    {
        return $this->planNumber;
    }

    public function getActionPlan(): ?string
    {
        return $this->actionPlan;
    }

    public function getReflection(): ?string
    {
        return $this->reflection;
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
            $this->planNumber,
            $this->actionPlan,
            $this->reflection,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function withProblemSolvingId(int $problemSolvingId): self
    {
        return new self(
            $this->id,
            $problemSolvingId,
            $this->planNumber,
            $this->actionPlan,
            $this->reflection,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function update(
        ?string $actionPlan,
        ?string $reflection
    ): self {
        return new self(
            $this->id,
            $this->problemSolvingId,
            $this->planNumber,
            $actionPlan,
            $reflection,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }

    /**
     * 振り返りが完了しているかどうか
     */
    public function isReflectionCompleted(): bool
    {
        return $this->reflection !== null && trim($this->reflection) !== '';
    }
}

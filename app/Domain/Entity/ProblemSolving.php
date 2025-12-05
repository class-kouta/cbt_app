<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class ProblemSolving
{
    private ?int $id;
    private string $problemSituation;
    private ?string $improvedImage;
    private ?string $actionPlan;
    private ?string $reflection;
    /** @var ProblemSolvingSolution[] */
    private array $solutions;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    /**
     * @param ProblemSolvingSolution[] $solutions
     */
    private function __construct(
        ?int $id,
        string $problemSituation,
        ?string $improvedImage,
        ?string $actionPlan,
        ?string $reflection,
        array $solutions,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->problemSituation = $problemSituation;
        $this->improvedImage = $improvedImage;
        $this->actionPlan = $actionPlan;
        $this->reflection = $reflection;
        $this->solutions = $solutions;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        string $problemSituation,
        ?string $improvedImage = null,
        ?string $actionPlan = null,
        ?string $reflection = null
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            $problemSituation,
            $improvedImage,
            $actionPlan,
            $reflection,
            [],
            $now,
            $now
        );
    }

    /**
     * @param ProblemSolvingSolution[] $solutions
     */
    public static function reconstitute(
        int $id,
        string $problemSituation,
        ?string $improvedImage,
        ?string $actionPlan,
        ?string $reflection,
        array $solutions,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $problemSituation,
            $improvedImage,
            $actionPlan,
            $reflection,
            $solutions,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProblemSituation(): string
    {
        return $this->problemSituation;
    }

    public function getImprovedImage(): ?string
    {
        return $this->improvedImage;
    }

    public function getActionPlan(): ?string
    {
        return $this->actionPlan;
    }

    public function getReflection(): ?string
    {
        return $this->reflection;
    }

    /**
     * @return ProblemSolvingSolution[]
     */
    public function getSolutions(): array
    {
        return $this->solutions;
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
            $this->problemSituation,
            $this->improvedImage,
            $this->actionPlan,
            $this->reflection,
            $this->solutions,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function update(
        string $problemSituation,
        ?string $improvedImage,
        ?string $actionPlan,
        ?string $reflection
    ): self {
        return new self(
            $this->id,
            $problemSituation,
            $improvedImage,
            $actionPlan,
            $reflection,
            $this->solutions,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }

    /**
     * @param ProblemSolvingSolution[] $solutions
     */
    public function withSolutions(array $solutions): self
    {
        return new self(
            $this->id,
            $this->problemSituation,
            $this->improvedImage,
            $this->actionPlan,
            $this->reflection,
            $solutions,
            $this->createdAt,
            $this->updatedAt
        );
    }
}

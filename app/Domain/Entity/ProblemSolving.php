<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class ProblemSolving
{
    private ?int $id;
    private string $problemSituation;
    private ?string $improvedImage;
    /** @var ProblemSolvingPlan[] */
    private array $plans;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    /**
     * @param ProblemSolvingPlan[] $plans
     */
    private function __construct(
        ?int $id,
        string $problemSituation,
        ?string $improvedImage,
        array $plans,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->problemSituation = $problemSituation;
        $this->improvedImage = $improvedImage;
        $this->plans = $plans;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        string $problemSituation,
        ?string $improvedImage = null
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            $problemSituation,
            $improvedImage,
            [],
            $now,
            $now
        );
    }

    /**
     * @param ProblemSolvingPlan[] $plans
     */
    public static function reconstitute(
        int $id,
        string $problemSituation,
        ?string $improvedImage,
        array $plans,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $problemSituation,
            $improvedImage,
            $plans,
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

    /**
     * @return ProblemSolvingPlan[]
     */
    public function getPlans(): array
    {
        return $this->plans;
    }

    /**
     * 最新の計画を取得
     */
    public function getLatestPlan(): ?ProblemSolvingPlan
    {
        if (empty($this->plans)) {
            return null;
        }
        
        $latestPlan = null;
        $maxNumber = 0;
        
        foreach ($this->plans as $plan) {
            if ($plan->getPlanNumber() > $maxNumber) {
                $maxNumber = $plan->getPlanNumber();
                $latestPlan = $plan;
            }
        }
        
        return $latestPlan;
    }

    /**
     * 新しい計画を追加できるかどうか
     */
    public function canAddNewPlan(): bool
    {
        $latest = $this->getLatestPlan();
        
        if ($latest === null) {
            return true;
        }
        
        return $latest->isReflectionCompleted();
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
            $this->plans,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function update(
        string $problemSituation,
        ?string $improvedImage
    ): self {
        return new self(
            $this->id,
            $problemSituation,
            $improvedImage,
            $this->plans,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }

    /**
     * @param ProblemSolvingPlan[] $plans
     */
    public function withPlans(array $plans): self
    {
        return new self(
            $this->id,
            $this->problemSituation,
            $this->improvedImage,
            $plans,
            $this->createdAt,
            $this->updatedAt
        );
    }
}

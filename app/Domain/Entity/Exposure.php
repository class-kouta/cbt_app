<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class Exposure
{
    /**
     * @param ExposureHierarchyItem[] $hierarchyItems
     * @param ExposureSession[] $sessions
     */
    private function __construct(
        private ?int $id,
        private string $avoidanceTarget,
        private ?string $exposureType,
        private ?string $selfTalk,
        private ?string $overallReflection,
        private ?string $nextGoal,
        private array $hierarchyItems,
        private array $sessions,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function createNew(
        string $avoidanceTarget,
        ?string $exposureType = null,
        ?string $selfTalk = null,
        ?string $overallReflection = null,
        ?string $nextGoal = null
    ): self {
        $now = new DateTimeImmutable('now');

        return new self(
            null,
            $avoidanceTarget,
            $exposureType,
            $selfTalk,
            $overallReflection,
            $nextGoal,
            [],
            [],
            $now,
            $now
        );
    }

    /**
     * @param ExposureHierarchyItem[] $hierarchyItems
     * @param ExposureSession[] $sessions
     */
    public static function reconstitute(
        int $id,
        string $avoidanceTarget,
        ?string $exposureType,
        ?string $selfTalk,
        ?string $overallReflection,
        ?string $nextGoal,
        array $hierarchyItems,
        array $sessions,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $avoidanceTarget,
            $exposureType,
            $selfTalk,
            $overallReflection,
            $nextGoal,
            $hierarchyItems,
            $sessions,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvoidanceTarget(): string
    {
        return $this->avoidanceTarget;
    }

    public function getExposureType(): ?string
    {
        return $this->exposureType;
    }

    public function getSelfTalk(): ?string
    {
        return $this->selfTalk;
    }

    public function getOverallReflection(): ?string
    {
        return $this->overallReflection;
    }

    public function getNextGoal(): ?string
    {
        return $this->nextGoal;
    }

    /**
     * @return ExposureHierarchyItem[]
     */
    public function getHierarchyItems(): array
    {
        return $this->hierarchyItems;
    }

    /**
     * @return ExposureSession[]
     */
    public function getSessions(): array
    {
        return $this->sessions;
    }

    public function getLatestSession(): ?ExposureSession
    {
        if (empty($this->sessions)) {
            return null;
        }

        $latest = null;
        $maxNumber = 0;

        foreach ($this->sessions as $session) {
            if ($session->getSessionNumber() > $maxNumber) {
                $maxNumber = $session->getSessionNumber();
                $latest = $session;
            }
        }

        return $latest;
    }

    public function canAddNewSession(): bool
    {
        $latest = $this->getLatestSession();

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

    public function update(
        string $avoidanceTarget,
        ?string $exposureType,
        ?string $selfTalk,
        ?string $overallReflection,
        ?string $nextGoal
    ): self {
        return new self(
            $this->id,
            $avoidanceTarget,
            $exposureType,
            $selfTalk,
            $overallReflection,
            $nextGoal,
            $this->hierarchyItems,
            $this->sessions,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}

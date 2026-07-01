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
        private ?string $notes,
        private array $hierarchyItems,
        private array $sessions,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function createNew(string $avoidanceTarget, ?string $notes = null): self
    {
        $now = new DateTimeImmutable('now');

        return new self(null, $avoidanceTarget, $notes, [], [], $now, $now);
    }

    /**
     * @param ExposureHierarchyItem[] $hierarchyItems
     * @param ExposureSession[] $sessions
     */
    public static function reconstitute(
        int $id,
        string $avoidanceTarget,
        ?string $notes,
        array $hierarchyItems,
        array $sessions,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, $avoidanceTarget, $notes, $hierarchyItems, $sessions, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvoidanceTarget(): string
    {
        return $this->avoidanceTarget;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
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

    public function update(string $avoidanceTarget, ?string $notes = null): self
    {
        return new self(
            $this->id,
            $avoidanceTarget,
            $notes,
            $this->hierarchyItems,
            $this->sessions,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}

<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class ExposureSession
{
    private function __construct(
        private ?int $id,
        private ?int $exposureId,
        private ?int $hierarchyItemId,
        private int $sessionNumber,
        private ?string $actionPlan,
        private ?int $sudsBefore,
        private ?int $sudsPeak,
        private ?int $sudsAfter,
        private ?DateTimeImmutable $performedAt,
        private ?string $reflection,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function createNew(
        int $sessionNumber,
        ?int $hierarchyItemId = null,
        ?string $actionPlan = null,
        ?int $sudsBefore = null,
        ?int $sudsPeak = null,
        ?int $sudsAfter = null,
        ?DateTimeImmutable $performedAt = null,
        ?string $reflection = null
    ): self {
        $now = new DateTimeImmutable('now');

        return new self(
            null,
            null,
            $hierarchyItemId,
            $sessionNumber,
            $actionPlan,
            $sudsBefore,
            $sudsPeak,
            $sudsAfter,
            $performedAt,
            $reflection,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        int $exposureId,
        ?int $hierarchyItemId,
        int $sessionNumber,
        ?string $actionPlan,
        ?int $sudsBefore,
        ?int $sudsPeak,
        ?int $sudsAfter,
        ?DateTimeImmutable $performedAt,
        ?string $reflection,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $exposureId,
            $hierarchyItemId,
            $sessionNumber,
            $actionPlan,
            $sudsBefore,
            $sudsPeak,
            $sudsAfter,
            $performedAt,
            $reflection,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExposureId(): ?int
    {
        return $this->exposureId;
    }

    public function getHierarchyItemId(): ?int
    {
        return $this->hierarchyItemId;
    }

    public function getSessionNumber(): int
    {
        return $this->sessionNumber;
    }

    public function getActionPlan(): ?string
    {
        return $this->actionPlan;
    }

    public function getSudsBefore(): ?int
    {
        return $this->sudsBefore;
    }

    public function getSudsPeak(): ?int
    {
        return $this->sudsPeak;
    }

    public function getSudsAfter(): ?int
    {
        return $this->sudsAfter;
    }

    public function getPerformedAt(): ?DateTimeImmutable
    {
        return $this->performedAt;
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

    public function isReflectionCompleted(): bool
    {
        return $this->reflection !== null && trim($this->reflection) !== '';
    }

    /**
     * バルク同期で新規セッションとして保存すべき入力かどうかを判定する。
     */
    public static function shouldPersistNewBulkItem(
        ?string $actionPlan,
        ?string $reflection,
        ?int $hierarchyItemId,
        ?int $sudsBefore,
        ?int $sudsPeak,
        ?int $sudsAfter,
        ?string $performedAt
    ): bool {
        if ($actionPlan !== null && trim($actionPlan) !== '') {
            return true;
        }

        if ($reflection !== null && trim($reflection) !== '') {
            return true;
        }

        if ($hierarchyItemId !== null) {
            return true;
        }

        if ($sudsBefore !== null) {
            return true;
        }

        if ($sudsPeak !== null) {
            return true;
        }

        if ($sudsAfter !== null) {
            return true;
        }

        if ($performedAt !== null && trim($performedAt) !== '') {
            return true;
        }

        return false;
    }

    public function update(
        ?int $hierarchyItemId,
        ?string $actionPlan,
        ?int $sudsBefore,
        ?int $sudsPeak,
        ?int $sudsAfter,
        ?DateTimeImmutable $performedAt,
        ?string $reflection
    ): self {
        return new self(
            $this->id,
            $this->exposureId,
            $hierarchyItemId,
            $this->sessionNumber,
            $actionPlan,
            $sudsBefore,
            $sudsPeak,
            $sudsAfter,
            $performedAt,
            $reflection,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }

    public function withSessionNumber(int $sessionNumber): self
    {
        return new self(
            $this->id,
            $this->exposureId,
            $this->hierarchyItemId,
            $sessionNumber,
            $this->actionPlan,
            $this->sudsBefore,
            $this->sudsPeak,
            $this->sudsAfter,
            $this->performedAt,
            $this->reflection,
            $this->createdAt,
            $this->updatedAt
        );
    }
}

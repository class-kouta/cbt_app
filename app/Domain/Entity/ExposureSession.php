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
        private ?int $sudsAfter,
        private ?string $reflection,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function createNew(
        int $sessionNumber,
        ?int $hierarchyItemId = null,
        ?int $sudsAfter = null,
        ?string $reflection = null
    ): self {
        $now = new DateTimeImmutable('now');

        return new self(
            null,
            null,
            $hierarchyItemId,
            $sessionNumber,
            $sudsAfter,
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
        ?int $sudsAfter,
        ?string $reflection,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $exposureId,
            $hierarchyItemId,
            $sessionNumber,
            $sudsAfter,
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

    public function getSudsAfter(): ?int
    {
        return $this->sudsAfter;
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
        ?string $reflection,
        ?int $hierarchyItemId,
        ?int $sudsAfter
    ): bool {
        if ($reflection !== null && trim($reflection) !== '') {
            return true;
        }

        if ($hierarchyItemId !== null) {
            return true;
        }

        if ($sudsAfter !== null) {
            return true;
        }

        return false;
    }

    public function update(
        ?int $hierarchyItemId,
        ?int $sudsAfter,
        ?string $reflection
    ): self {
        return new self(
            $this->id,
            $this->exposureId,
            $hierarchyItemId,
            $this->sessionNumber,
            $sudsAfter,
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
            $this->sudsAfter,
            $this->reflection,
            $this->createdAt,
            $this->updatedAt
        );
    }
}

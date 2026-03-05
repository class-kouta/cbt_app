<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class ModeMap
{
    private ?int $id;

    private ?string $woundedChildMode;

    private ?string $hurtfulAdultMode;

    private ?string $unacceptableCopingMode;

    private ?string $healthyHappyChildMode;

    private ?string $healthyAdultMode;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ?string $woundedChildMode,
        ?string $hurtfulAdultMode,
        ?string $unacceptableCopingMode,
        ?string $healthyHappyChildMode,
        ?string $healthyAdultMode,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->woundedChildMode = $woundedChildMode;
        $this->hurtfulAdultMode = $hurtfulAdultMode;
        $this->unacceptableCopingMode = $unacceptableCopingMode;
        $this->healthyHappyChildMode = $healthyHappyChildMode;
        $this->healthyAdultMode = $healthyAdultMode;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        ?string $woundedChildMode,
        ?string $hurtfulAdultMode,
        ?string $unacceptableCopingMode,
        ?string $healthyHappyChildMode,
        ?string $healthyAdultMode
    ): self {
        $now = new DateTimeImmutable('now');

        return new self(null, $woundedChildMode, $hurtfulAdultMode, $unacceptableCopingMode, $healthyHappyChildMode, $healthyAdultMode, $now, $now);
    }

    public static function reconstitute(
        int $id,
        ?string $woundedChildMode,
        ?string $hurtfulAdultMode,
        ?string $unacceptableCopingMode,
        ?string $healthyHappyChildMode,
        ?string $healthyAdultMode,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, $woundedChildMode, $hurtfulAdultMode, $unacceptableCopingMode, $healthyHappyChildMode, $healthyAdultMode, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWoundedChildMode(): ?string
    {
        return $this->woundedChildMode;
    }

    public function getHurtfulAdultMode(): ?string
    {
        return $this->hurtfulAdultMode;
    }

    public function getUnacceptableCopingMode(): ?string
    {
        return $this->unacceptableCopingMode;
    }

    public function getHealthyHappyChildMode(): ?string
    {
        return $this->healthyHappyChildMode;
    }

    public function getHealthyAdultMode(): ?string
    {
        return $this->healthyAdultMode;
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
            $this->woundedChildMode,
            $this->hurtfulAdultMode,
            $this->unacceptableCopingMode,
            $this->healthyHappyChildMode,
            $this->healthyAdultMode,
            $this->createdAt,
            $this->updatedAt
        );
    }
}

<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\ValueObject\SupportNetworkName;

class SupportNetwork
{
    private ?int $id;
    private SupportNetworkName $name;
    private int $point;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        SupportNetworkName $name,
        int $point,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->point = $point;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(string $name): self
    {
        $now = new DateTimeImmutable('now');
        return new self(null, new SupportNetworkName($name), 0, $now, $now);
    }

    public static function reconstitute(
        int $id,
        string $name,
        int $point,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, new SupportNetworkName($name), $point, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name->value();
    }

    public function getPoint(): int
    {
        return $this->point;
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
        return new self($id, $this->name, $this->point, $this->createdAt, $this->updatedAt);
    }

    public function incrementPoint(): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->point + 1,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }

    public function decrementPoint(): self
    {
        $newPoint = max(0, $this->point - 1);
        return new self(
            $this->id,
            $this->name,
            $newPoint,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }

    public function updateName(string $name, ?int $point = null): self
    {
        return new self(
            $this->id,
            new SupportNetworkName($name),
            $point ?? $this->point,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}

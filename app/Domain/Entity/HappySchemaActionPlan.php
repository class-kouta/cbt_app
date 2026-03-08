<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class HappySchemaActionPlan
{
    private ?int $id;

    private ?string $happySchema;

    private ?string $actionPlan;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ?string $happySchema,
        ?string $actionPlan,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->happySchema = $happySchema;
        $this->actionPlan = $actionPlan;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        ?string $happySchema,
        ?string $actionPlan
    ): self {
        $now = new DateTimeImmutable('now');

        return new self(null, $happySchema, $actionPlan, $now, $now);
    }

    public static function reconstitute(
        int $id,
        ?string $happySchema,
        ?string $actionPlan,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, $happySchema, $actionPlan, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHappySchema(): ?string
    {
        return $this->happySchema;
    }

    public function getActionPlan(): ?string
    {
        return $this->actionPlan;
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
            $this->happySchema,
            $this->actionPlan,
            $this->createdAt,
            $this->updatedAt
        );
    }
}

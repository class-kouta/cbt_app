<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class StressPersonEncyclopedia
{
    private function __construct(
        private ?int $id,
        private string $name,
        private ?string $relationship,
        private ?string $difficultTraits,
        private ?string $myReaction,
        private ?string $copingStrategy,
        private ?string $notes,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    public static function createNew(
        string $name,
        ?string $relationship,
        ?string $difficultTraits,
        ?string $myReaction,
        ?string $copingStrategy,
        ?string $notes,
        DateTimeImmutable $createdAt,
    ): self {
        return new self(
            null,
            $name,
            $relationship,
            $difficultTraits,
            $myReaction,
            $copingStrategy,
            $notes,
            $createdAt,
            $createdAt,
        );
    }

    public static function reconstitute(
        int $id,
        string $name,
        ?string $relationship,
        ?string $difficultTraits,
        ?string $myReaction,
        ?string $copingStrategy,
        ?string $notes,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            $id,
            $name,
            $relationship,
            $difficultTraits,
            $myReaction,
            $copingStrategy,
            $notes,
            $createdAt,
            $updatedAt,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function getDifficultTraits(): ?string
    {
        return $this->difficultTraits;
    }

    public function getMyReaction(): ?string
    {
        return $this->myReaction;
    }

    public function getCopingStrategy(): ?string
    {
        return $this->copingStrategy;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
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
            $this->name,
            $this->relationship,
            $this->difficultTraits,
            $this->myReaction,
            $this->copingStrategy,
            $this->notes,
            $this->createdAt,
            $this->updatedAt,
        );
    }

    public function update(
        string $name,
        ?string $relationship,
        ?string $difficultTraits,
        ?string $myReaction,
        ?string $copingStrategy,
        ?string $notes,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            $this->id,
            $name,
            $relationship,
            $difficultTraits,
            $myReaction,
            $copingStrategy,
            $notes,
            $this->createdAt,
            $updatedAt,
        );
    }
}

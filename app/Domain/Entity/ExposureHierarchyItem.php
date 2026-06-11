<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class ExposureHierarchyItem
{
    private function __construct(
        private ?int $id,
        private ?int $exposureId,
        private string $content,
        private ?int $expectedSuds,
        private int $sortOrder,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function createNew(
        string $content,
        int $sortOrder,
        ?int $expectedSuds = null
    ): self {
        $now = new DateTimeImmutable('now');

        return new self(null, null, $content, $expectedSuds, $sortOrder, $now, $now);
    }

    public static function reconstitute(
        int $id,
        int $exposureId,
        string $content,
        ?int $expectedSuds,
        int $sortOrder,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, $exposureId, $content, $expectedSuds, $sortOrder, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExposureId(): ?int
    {
        return $this->exposureId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getExpectedSuds(): ?int
    {
        return $this->expectedSuds;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
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
            $this->exposureId,
            $this->content,
            $this->expectedSuds,
            $this->sortOrder,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function update(string $content, int $sortOrder, ?int $expectedSuds): self
    {
        return new self(
            $this->id,
            $this->exposureId,
            $content,
            $expectedSuds,
            $sortOrder,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}

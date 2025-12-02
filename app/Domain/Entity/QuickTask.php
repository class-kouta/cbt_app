<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\ValueObject\QuickTaskContent;

class QuickTask
{
    private ?int $id;
    private QuickTaskContent $content;
    private ?int $difficultyId;
    /** @var int[] */
    private array $tagIds;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        QuickTaskContent $content,
        ?int $difficultyId,
        array $tagIds,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->difficultyId = $difficultyId;
        $this->tagIds = array_values(array_unique(array_map('intval', $tagIds)));
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(string $content, ?int $difficultyId = null, array $tagIds = []): self
    {
        $now = new DateTimeImmutable('now');
        return new self(null, new QuickTaskContent($content), $difficultyId, $tagIds, $now, $now);
    }

    public static function reconstitute(
        int $id,
        string $content,
        ?int $difficultyId,
        array $tagIds,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, new QuickTaskContent($content), $difficultyId, $tagIds, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content->value();
    }

    public function getDifficultyId(): ?int
    {
        return $this->difficultyId;
    }

    /**
     * @return int[]
     */
    public function getTagIds(): array
    {
        return $this->tagIds;
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
        return new self($id, $this->content, $this->difficultyId, $this->tagIds, $this->createdAt, $this->updatedAt);
    }

    public function updateContent(string $content, ?int $difficultyId = null, array $tagIds = []): self
    {
        return new self(
            $this->id,
            new QuickTaskContent($content),
            $difficultyId,
            $tagIds,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}

<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\ValueObject\TodoContent;

class Todo
{
    private ?int $id;
    private int $difficultyId;
    private TodoContent $content;
    private ?DateTimeImmutable $completedAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    /** @var int[] */
    private array $tagIds;

    private function __construct(
        ?int $id,
        int $difficultyId,
        TodoContent $content,
        ?DateTimeImmutable $completedAt,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        array $tagIds = []
    ) {
        $this->id = $id;
        $this->difficultyId = $difficultyId;
        $this->content = $content;
        $this->completedAt = $completedAt;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->tagIds = array_values(array_unique(array_map('intval', $tagIds)));
    }

    public static function createNew(int $difficultyId, string $content, array $tagIds = []): self
    {
        $now = new DateTimeImmutable('now');
        return new self(null, $difficultyId, new TodoContent($content), null, $now, $now, $tagIds);
    }

    public static function reconstitute(
        int $id,
        int $difficultyId,
        string $content,
        ?DateTimeImmutable $completedAt,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        array $tagIds = []
    ): self {
        return new self($id, $difficultyId, new TodoContent($content), $completedAt, $createdAt, $updatedAt, $tagIds);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDifficultyId(): int
    {
        return $this->difficultyId;
    }

    public function getContent(): string
    {
        return $this->content->value();
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return int[]
     */
    public function getTagIds(): array
    {
        return $this->tagIds;
    }

    public function withId(int $id): self
    {
        return new self($id, $this->difficultyId, $this->content, $this->completedAt, $this->createdAt, $this->updatedAt, $this->tagIds);
    }

    public function touchUpdated(DateTimeImmutable $updatedAt): self
    {
        return new self($this->id, $this->difficultyId, $this->content, $this->completedAt, $this->createdAt, $updatedAt, $this->tagIds);
    }
}

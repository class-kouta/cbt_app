<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\ValueObject\CopingContent;

class Coping
{
    private ?int $id;
    private CopingContent $content;
    private int $point;
    private int $sortOrder;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    /** @var int[] */
    private array $copingTagIds;

    private function __construct(
        ?int $id,
        CopingContent $content,
        int $point,
        int $sortOrder,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        array $copingTagIds = []
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->point = $point;
        $this->sortOrder = $sortOrder;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->copingTagIds = array_values(array_unique(array_map('intval', $copingTagIds)));
    }

    public static function createNew(string $content, array $copingTagIds = [], int $sortOrder = 0): self
    {
        $now = new DateTimeImmutable('now');
        return new self(null, new CopingContent($content), 0, $sortOrder, $now, $now, $copingTagIds);
    }

    public static function reconstitute(
        int $id,
        string $content,
        int $point,
        int $sortOrder,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        array $copingTagIds = []
    ): self {
        return new self($id, new CopingContent($content), $point, $sortOrder, $createdAt, $updatedAt, $copingTagIds);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content->value();
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

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return int[]
     */
    public function getCopingTagIds(): array
    {
        return $this->copingTagIds;
    }

    public function withId(int $id): self
    {
        return new self($id, $this->content, $this->point, $this->sortOrder, $this->createdAt, $this->updatedAt, $this->copingTagIds);
    }

    public function withSortOrder(int $sortOrder): self
    {
        return new self($this->id, $this->content, $this->point, $sortOrder, $this->createdAt, $this->updatedAt, $this->copingTagIds);
    }

    public function incrementPoint(): self
    {
        return new self(
            $this->id,
            $this->content,
            $this->point + 1,
            $this->sortOrder,
            $this->createdAt,
            new DateTimeImmutable('now'),
            $this->copingTagIds
        );
    }

    public function decrementPoint(): self
    {
        $newPoint = max(0, $this->point - 1);
        return new self(
            $this->id,
            $this->content,
            $newPoint,
            $this->sortOrder,
            $this->createdAt,
            new DateTimeImmutable('now'),
            $this->copingTagIds
        );
    }

    public function updateContent(string $content, array $copingTagIds, ?int $point = null): self
    {
        return new self(
            $this->id,
            new CopingContent($content),
            $point ?? $this->point,
            $this->sortOrder,
            $this->createdAt,
            new DateTimeImmutable('now'),
            $copingTagIds
        );
    }
}

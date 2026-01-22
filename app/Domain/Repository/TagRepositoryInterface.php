<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Tag;

interface TagRepositoryInterface
{
    public function save(Tag $tag): Tag;

    public function findById(int $id): ?Tag;

    /**
     * 全てのタグを取得
     *
     * @return Tag[]
     */
    public function findAll(): array;

    public function delete(int $id): void;
}

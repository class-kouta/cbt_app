<?php

namespace App\Domain\Repository;

use App\Domain\Entity\AnxietyDiary;

interface AnxietyDiaryRepositoryInterface
{
    public function save(AnxietyDiary $anxietyDiary): AnxietyDiary;

    public function findById(int $id): ?AnxietyDiary;

    public function delete(int $id): void;

    /**
     * @return AnxietyDiary[]
     */
    public function findAll(): array;

    /**
     * キーワード検索
     *
     * @param string|null $keyword 検索キーワード
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果（配列形式）
     */
    public function search(?string $keyword, array $searchableColumns): array;
}

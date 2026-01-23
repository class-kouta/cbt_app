<?php

namespace App\Domain\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Entity\Column;

interface ColumnRepositoryInterface
{
    public function save(Column $column): Column;

    public function findById(int $id): ?Column;

    public function delete(int $id): void;

    /**
     * @return Column[]
     */
    public function findAll(): array;

    /**
     * 検索条件に基づいてコラムを検索
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果（配列形式）
     */
    public function search(SearchCriteriaData $criteria, array $searchableColumns): array;
}

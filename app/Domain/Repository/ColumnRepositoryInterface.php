<?php

namespace App\Domain\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Entity\Column;

interface ColumnRepositoryInterface
{
    public function save(Column $column): Column;

    /**
     * コラムを保存し、タグを同期する
     *
     * @param Column $column コラムエンティティ
     * @param array<int> $tagIds タグIDの配列
     * @return array<string, mixed> 保存結果（タグ情報を含む）
     */
    public function saveWithTags(Column $column, array $tagIds): array;

    public function findById(int $id): ?Column;

    public function delete(int $id): void;

    /**
     * @return Column[]
     */
    public function findAll(): array;

    /**
     * 検索条件に基づいてコラムを検索（ページネーション対応）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<string, mixed> 検索結果（ページネーション情報を含む）
     */
    public function search(SearchCriteriaData $criteria, array $searchableColumns): array;

    /**
     * 検索条件に基づいてコラムを全件取得（CSV出力用）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function searchAll(SearchCriteriaData $criteria, array $searchableColumns): array;
}

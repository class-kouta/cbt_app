<?php

namespace App\Domain\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Entity\Column;

interface ColumnRepositoryInterface
{
    public function saveForMember(Column $column, int $memberId): Column;

    /**
     * コラムを保存し、タグを同期する
     *
     * @param Column $column コラムエンティティ
     * @param array<int> $tagIds タグIDの配列
     * @return array<string, mixed> 保存結果（タグ情報を含む）
     */
    public function saveWithTagsForMember(Column $column, array $tagIds, int $memberId): array;

    public function findByIdForMember(int $id, int $memberId): ?Column;

    public function deleteForMember(int $id, int $memberId): void;

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
    public function searchForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    /**
     * 検索条件に基づいてコラムを全件取得（CSV出力用）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function searchAllForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;
}

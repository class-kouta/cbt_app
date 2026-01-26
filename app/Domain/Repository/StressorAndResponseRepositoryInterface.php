<?php

namespace App\Domain\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Entity\StressorAndResponse;

interface StressorAndResponseRepositoryInterface
{
    public function save(StressorAndResponse $stressorAndResponse): StressorAndResponse;

    public function findById(int $id): ?StressorAndResponse;

    public function delete(int $id): void;

    /**
     * @return StressorAndResponse[]
     */
    public function findAll(): array;

    /**
     * 検索条件に基づいてストレッサーとストレス反応を検索（ページネーション対応）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<string, mixed> 検索結果（ページネーション情報を含む）
     */
    public function search(SearchCriteriaData $criteria, array $searchableColumns): array;

    /**
     * 検索条件に基づいてストレッサーとストレス反応を全件取得（CSV出力用）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function searchAll(SearchCriteriaData $criteria, array $searchableColumns): array;
}

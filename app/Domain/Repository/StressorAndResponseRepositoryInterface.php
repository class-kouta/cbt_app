<?php

namespace App\Domain\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Entity\StressorAndResponse;

interface StressorAndResponseRepositoryInterface
{
    public function saveForMember(StressorAndResponse $stressorAndResponse, int $memberId): StressorAndResponse;

    public function findByIdForMember(int $id, int $memberId): ?StressorAndResponse;

    public function deleteForMember(int $id, int $memberId): void;

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
    public function searchForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    /**
     * 検索条件に基づいてストレッサーとストレス反応を全件取得（CSV出力用）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function searchAllForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;
}

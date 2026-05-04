<?php

namespace App\Domain\Repository;

use App\Application\DTO\PlanSearchCriteriaData;
use App\Application\DTO\SearchCriteriaData;
use App\Domain\Entity\ProblemSolving;
use App\Domain\Entity\ProblemSolvingSolution;
use App\Domain\Entity\ProblemSolvingPlan;

interface ProblemSolvingRepositoryInterface
{
    public function saveForMember(ProblemSolving $problemSolving, int $memberId): ProblemSolving;

    public function findByIdForMember(int $id, int $memberId): ?ProblemSolving;

    public function deleteForMember(int $id, int $memberId): void;

    /**
     * @return ProblemSolving[]
     */
    public function findAll(): array;

    /**
     * 検索条件に基づいて問題解決法を検索（ページネーション対応）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<string, mixed> 検索結果（ページネーション情報を含む）
     */
    public function searchForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    /**
     * 検索条件に基づいて問題解決法を全件取得（CSV出力用）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function searchAllForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    public function saveSolution(int $problemSolvingId, ProblemSolvingSolution $solution): ProblemSolvingSolution;

    public function updateSolution(ProblemSolvingSolution $solution): ProblemSolvingSolution;

    public function deleteSolution(int $solutionId): void;

    /**
     * 検索条件に基づいて計画を検索（ページネーション対応）
     *
     * @param PlanSearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<string, mixed> 検索結果（ページネーション情報を含む）
     */
    public function searchPlansForMember(PlanSearchCriteriaData $criteria, array $searchableColumns, int $memberId): array;

    public function savePlan(int $problemSolvingId, ProblemSolvingPlan $plan): ProblemSolvingPlan;

    public function findPlanById(int $planId): ?ProblemSolvingPlan;

    public function updatePlan(ProblemSolvingPlan $plan): ProblemSolvingPlan;

    public function deletePlan(int $planId): void;
}

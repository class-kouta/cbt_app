<?php

namespace App\Domain\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Entity\ProblemSolving;
use App\Domain\Entity\ProblemSolvingSolution;
use App\Domain\Entity\ProblemSolvingPlan;

interface ProblemSolvingRepositoryInterface
{
    public function save(ProblemSolving $problemSolving): ProblemSolving;

    public function findById(int $id): ?ProblemSolving;

    public function delete(int $id): void;

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
    public function search(SearchCriteriaData $criteria, array $searchableColumns): array;

    /**
     * 検索条件に基づいて問題解決法を全件取得（CSV出力用）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function searchAll(SearchCriteriaData $criteria, array $searchableColumns): array;

    public function saveSolution(int $problemSolvingId, ProblemSolvingSolution $solution): ProblemSolvingSolution;

    public function updateSolution(ProblemSolvingSolution $solution): ProblemSolvingSolution;

    public function deleteSolution(int $solutionId): void;

    public function savePlan(int $problemSolvingId, ProblemSolvingPlan $plan): ProblemSolvingPlan;

    public function findPlanById(int $planId): ?ProblemSolvingPlan;

    public function updatePlan(ProblemSolvingPlan $plan): ProblemSolvingPlan;

    public function deletePlan(int $planId): void;
}

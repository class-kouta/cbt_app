<?php

namespace App\Domain\Repository;

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

    public function saveSolution(int $problemSolvingId, ProblemSolvingSolution $solution): ProblemSolvingSolution;

    public function updateSolution(ProblemSolvingSolution $solution): ProblemSolvingSolution;

    public function deleteSolution(int $solutionId): void;

    public function savePlan(int $problemSolvingId, ProblemSolvingPlan $plan): ProblemSolvingPlan;

    public function findPlanById(int $planId): ?ProblemSolvingPlan;

    public function updatePlan(ProblemSolvingPlan $plan): ProblemSolvingPlan;

    public function deletePlan(int $planId): void;
}

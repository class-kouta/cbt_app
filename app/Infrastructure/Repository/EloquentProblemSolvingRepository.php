<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\ProblemSolving as ProblemSolvingEntity;
use App\Domain\Entity\ProblemSolvingSolution as ProblemSolvingSolutionEntity;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use App\Infrastructure\Database\Models\ProblemSolving as ProblemSolvingModel;
use App\Infrastructure\Database\Models\ProblemSolvingSolution as ProblemSolvingSolutionModel;
use DateTimeImmutable;

class EloquentProblemSolvingRepository implements ProblemSolvingRepositoryInterface
{
    public function save(ProblemSolvingEntity $problemSolving): ProblemSolvingEntity
    {
        if ($problemSolving->getId() !== null) {
            // 更新
            $model = ProblemSolvingModel::findOrFail($problemSolving->getId());
            $model->problem_situation = $problemSolving->getProblemSituation();
            $model->improved_image = $problemSolving->getImprovedImage();
            $model->action_plan = $problemSolving->getActionPlan();
            $model->reflection = $problemSolving->getReflection();
            $model->save();
            $model->load('solutions'); // リレーションを再読み込み
        } else {
            // 新規作成
            $model = new ProblemSolvingModel();
            $model->problem_situation = $problemSolving->getProblemSituation();
            $model->improved_image = $problemSolving->getImprovedImage();
            $model->action_plan = $problemSolving->getActionPlan();
            $model->reflection = $problemSolving->getReflection();
            $model->save();
        }

        return $this->toEntity($model);
    }

    public function findById(int $id): ?ProblemSolvingEntity
    {
        $model = ProblemSolvingModel::with('solutions')->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        $model = ProblemSolvingModel::find($id);

        if ($model !== null) {
            $model->delete();
        }
    }

    /**
     * @return ProblemSolvingEntity[]
     */
    public function findAll(): array
    {
        return ProblemSolvingModel::with('solutions')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => $this->toEntity($model))
            ->toArray();
    }

    public function saveSolution(int $problemSolvingId, ProblemSolvingSolutionEntity $solution): ProblemSolvingSolutionEntity
    {
        $model = new ProblemSolvingSolutionModel();
        $model->problem_solving_id = $problemSolvingId;
        $model->content = $solution->getContent();
        $model->effectiveness = $solution->getEffectiveness();
        $model->feasibility = $solution->getFeasibility();
        $model->sort_order = $solution->getSortOrder();
        $model->save();

        return $this->toSolutionEntity($model);
    }

    public function updateSolution(ProblemSolvingSolutionEntity $solution): ProblemSolvingSolutionEntity
    {
        $model = ProblemSolvingSolutionModel::findOrFail($solution->getId());
        $model->content = $solution->getContent();
        $model->effectiveness = $solution->getEffectiveness();
        $model->feasibility = $solution->getFeasibility();
        $model->sort_order = $solution->getSortOrder();
        $model->save();

        return $this->toSolutionEntity($model);
    }

    public function deleteSolution(int $solutionId): void
    {
        $model = ProblemSolvingSolutionModel::find($solutionId);

        if ($model !== null) {
            $model->delete();
        }
    }

    private function toEntity(ProblemSolvingModel $model): ProblemSolvingEntity
    {
        $solutions = $model->solutions->map(fn ($s) => $this->toSolutionEntity($s))->toArray();

        return ProblemSolvingEntity::reconstitute(
            id: (int) $model->id,
            problemSituation: (string) $model->problem_situation,
            improvedImage: $model->improved_image,
            actionPlan: $model->action_plan,
            reflection: $model->reflection,
            solutions: $solutions,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    private function toSolutionEntity(ProblemSolvingSolutionModel $model): ProblemSolvingSolutionEntity
    {
        return ProblemSolvingSolutionEntity::reconstitute(
            id: (int) $model->id,
            problemSolvingId: (int) $model->problem_solving_id,
            content: (string) $model->content,
            effectiveness: $model->effectiveness,
            feasibility: $model->feasibility,
            sortOrder: (int) $model->sort_order,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }
}

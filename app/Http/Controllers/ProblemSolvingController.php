<?php

namespace App\Http\Controllers;

use App\Application\DTO\ProblemSolvingData;
use App\Application\DTO\ProblemSolvingSolutionData;
use App\Application\DTO\ProblemSolvingPlanData;
use App\Application\UseCase\ProblemSolving\CreateProblemSolvingUseCase;
use App\Application\UseCase\ProblemSolving\UpdateProblemSolvingUseCase;
use App\Application\UseCase\ProblemSolving\DeleteProblemSolvingUseCase;
use App\Application\UseCase\ProblemSolving\ExportProblemSolvingCsvUseCase;
use App\Application\UseCase\ProblemSolving\AddSolutionUseCase;
use App\Application\UseCase\ProblemSolving\UpdateSolutionUseCase;
use App\Application\UseCase\ProblemSolving\DeleteSolutionUseCase;
use App\Application\UseCase\ProblemSolving\AddPlanUseCase;
use App\Application\UseCase\ProblemSolving\UpdatePlanUseCase;
use App\Application\UseCase\ProblemSolving\DeletePlanUseCase;
use App\Application\UseCase\ProblemSolving\SearchProblemSolvingUseCase;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\ProblemSolving\CreateProblemSolvingRequest;
use App\Http\Requests\ProblemSolving\UpdateProblemSolvingRequest;
use App\Http\Requests\ProblemSolving\AddSolutionRequest;
use App\Http\Requests\ProblemSolving\UpdateSolutionRequest;
use App\Http\Requests\ProblemSolving\AddPlanRequest;
use App\Http\Requests\ProblemSolving\UpdatePlanRequest;
use App\Infrastructure\Database\Models\ProblemSolving;
use App\Infrastructure\Database\Models\ProblemSolvingSolution;
use App\Infrastructure\Database\Models\ProblemSolvingPlan;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProblemSolvingController extends Controller
{
    /**
     * 問題解決一覧を取得（作成日時降順）
     * キーワード検索とタグ検索に対応
     */
    public function index(SearchRequest $request, SearchProblemSolvingUseCase $searchUseCase): JsonResponse
    {
        $criteria = $request->toSearchCriteriaData();
        $problemSolvings = $searchUseCase->handle($criteria);

        return response()->json($problemSolvings);
    }

    /**
     * 問題解決詳細を取得
     */
    public function show(ProblemSolving $problemSolving): JsonResponse
    {
        $problemSolving->load(['solutions', 'plans', 'tags']);
        return response()->json($this->formatProblemSolving($problemSolving));
    }

    /**
     * 計画一覧を取得（全件、作成日時降順）
     * 実行計画が入力されている計画のみを対象とする
     */
    public function plans(): JsonResponse
    {
        $plans = ProblemSolvingPlan::with('problemSolving')
            ->whereNotNull('action_plan')
            ->where('action_plan', '!=', '')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'problem_solving_id' => $plan->problem_solving_id,
                    'problem_situation' => $plan->problemSolving->problem_situation ?? '',
                    'plan_number' => $plan->plan_number,
                    'action_plan' => $plan->action_plan,
                    'reflection' => $plan->reflection,
                    'created_at' => $plan->created_at->format(DATE_ATOM),
                    'updated_at' => $plan->updated_at->format(DATE_ATOM),
                ];
            });

        return response()->json($plans);
    }

    /**
     * 問題解決を作成
     */
    public function store(CreateProblemSolvingRequest $request, CreateProblemSolvingUseCase $createProblemSolving): JsonResponse
    {
        $data = new ProblemSolvingData(
            problemSituation: (string) $request->string('problem_situation'),
            improvedImage: $request->filled('improved_image') ? (string) $request->string('improved_image') : null
        );

        $problemSolvingEntity = $createProblemSolving->handle($data);

        // タグの紐付け
        $tagIds = $request->input('tag_ids', []);
        $model = ProblemSolving::with('tags')->find($problemSolvingEntity->getId());
        if (!empty($tagIds)) {
            $model->tags()->sync($tagIds);
            $model->load('tags');
        }

        return response()->json([
            'id' => $problemSolvingEntity->getId(),
            'problem_situation' => $problemSolvingEntity->getProblemSituation(),
            'improved_image' => $problemSolvingEntity->getImprovedImage(),
            'solutions' => [],
            'plans' => [],
            'tags' => $model->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $model->tags->pluck('id')->toArray(),
            'created_at' => $problemSolvingEntity->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $problemSolvingEntity->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * 問題解決を更新
     */
    public function update(UpdateProblemSolvingRequest $request, ProblemSolving $problemSolving, UpdateProblemSolvingUseCase $updateProblemSolving): JsonResponse
    {
        $data = new ProblemSolvingData(
            problemSituation: (string) $request->string('problem_situation'),
            improvedImage: $request->filled('improved_image') ? (string) $request->string('improved_image') : null
        );

        $updated = $updateProblemSolving->handle($problemSolving->id, $data);

        // タグの同期
        $tagIds = $request->input('tag_ids', []);
        $problemSolving->tags()->sync($tagIds);
        $problemSolving->load('tags');

        return response()->json([
            'id' => $updated->getId(),
            'problem_situation' => $updated->getProblemSituation(),
            'improved_image' => $updated->getImprovedImage(),
            'solutions' => array_map(fn ($s) => [
                'id' => $s->getId(),
                'content' => $s->getContent(),
                'effectiveness' => $s->getEffectiveness(),
                'feasibility' => $s->getFeasibility(),
                'sort_order' => $s->getSortOrder(),
            ], $updated->getSolutions()),
            'plans' => array_map(fn ($p) => [
                'id' => $p->getId(),
                'plan_number' => $p->getPlanNumber(),
                'action_plan' => $p->getActionPlan(),
                'reflection' => $p->getReflection(),
            ], $updated->getPlans()),
            'tags' => $problemSolving->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $problemSolving->tags->pluck('id')->toArray(),
            'created_at' => $updated->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updated->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * 問題解決を削除
     */
    public function destroy(ProblemSolving $problemSolving, DeleteProblemSolvingUseCase $deleteProblemSolving): JsonResponse
    {
        $deleteProblemSolving->handle($problemSolving->id);

        return response()->json(null, 204);
    }

    /**
     * 解決策を追加
     */
    public function addSolution(AddSolutionRequest $request, ProblemSolving $problemSolving, AddSolutionUseCase $addSolution): JsonResponse
    {
        $data = new ProblemSolvingSolutionData(
            content: (string) $request->string('content'),
            sortOrder: (int) $request->integer('sort_order'),
            effectiveness: $request->filled('effectiveness') ? (int) $request->integer('effectiveness') : null,
            feasibility: $request->filled('feasibility') ? (int) $request->integer('feasibility') : null
        );

        $solution = $addSolution->handle($problemSolving->id, $data);

        return response()->json([
            'id' => $solution->getId(),
            'problem_solving_id' => $solution->getProblemSolvingId(),
            'content' => $solution->getContent(),
            'effectiveness' => $solution->getEffectiveness(),
            'feasibility' => $solution->getFeasibility(),
            'sort_order' => $solution->getSortOrder(),
            'created_at' => $solution->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $solution->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * 解決策を更新
     */
    public function updateSolution(UpdateSolutionRequest $request, ProblemSolving $problemSolving, ProblemSolvingSolution $solution, UpdateSolutionUseCase $updateSolution): JsonResponse
    {
        $data = new ProblemSolvingSolutionData(
            content: (string) $request->string('content'),
            sortOrder: (int) $request->integer('sort_order'),
            effectiveness: $request->filled('effectiveness') ? (int) $request->integer('effectiveness') : null,
            feasibility: $request->filled('feasibility') ? (int) $request->integer('feasibility') : null
        );

        $updated = $updateSolution->handle($solution->id, $data);

        return response()->json([
            'id' => $updated->getId(),
            'problem_solving_id' => $updated->getProblemSolvingId(),
            'content' => $updated->getContent(),
            'effectiveness' => $updated->getEffectiveness(),
            'feasibility' => $updated->getFeasibility(),
            'sort_order' => $updated->getSortOrder(),
            'created_at' => $updated->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updated->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * 解決策を削除
     */
    public function deleteSolution(ProblemSolving $problemSolving, ProblemSolvingSolution $solution, DeleteSolutionUseCase $deleteSolution): JsonResponse
    {
        $deleteSolution->handle($solution->id);

        return response()->json(null, 204);
    }

    /**
     * 計画を追加
     */
    public function addPlan(AddPlanRequest $request, ProblemSolving $problemSolving, AddPlanUseCase $addPlan): JsonResponse
    {
        $data = new ProblemSolvingPlanData(
            actionPlan: $request->filled('action_plan') ? (string) $request->string('action_plan') : null,
            reflection: $request->filled('reflection') ? (string) $request->string('reflection') : null
        );

        $plan = $addPlan->handle($problemSolving->id, $data);

        return response()->json([
            'id' => $plan->getId(),
            'problem_solving_id' => $plan->getProblemSolvingId(),
            'plan_number' => $plan->getPlanNumber(),
            'action_plan' => $plan->getActionPlan(),
            'reflection' => $plan->getReflection(),
            'created_at' => $plan->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $plan->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * 計画を更新
     */
    public function updatePlan(UpdatePlanRequest $request, ProblemSolving $problemSolving, ProblemSolvingPlan $plan, UpdatePlanUseCase $updatePlan): JsonResponse
    {
        $data = new ProblemSolvingPlanData(
            actionPlan: $request->filled('action_plan') ? (string) $request->string('action_plan') : null,
            reflection: $request->filled('reflection') ? (string) $request->string('reflection') : null
        );

        $updated = $updatePlan->handle($plan->id, $data);

        return response()->json([
            'id' => $updated->getId(),
            'problem_solving_id' => $updated->getProblemSolvingId(),
            'plan_number' => $updated->getPlanNumber(),
            'action_plan' => $updated->getActionPlan(),
            'reflection' => $updated->getReflection(),
            'created_at' => $updated->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updated->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * 計画を削除
     */
    public function deletePlan(ProblemSolving $problemSolving, ProblemSolvingPlan $plan, DeletePlanUseCase $deletePlan): JsonResponse
    {
        $deletePlan->handle($plan->id);

        return response()->json(null, 204);
    }

    /**
     * 問題解決をJSON形式にフォーマット
     */
    private function formatProblemSolving(ProblemSolving $problemSolving): array
    {
        return [
            'id' => $problemSolving->id,
            'problem_situation' => $problemSolving->problem_situation,
            'improved_image' => $problemSolving->improved_image,
            'solutions' => $problemSolving->solutions->map(fn ($s) => [
                'id' => $s->id,
                'content' => $s->content,
                'effectiveness' => $s->effectiveness,
                'feasibility' => $s->feasibility,
                'sort_order' => $s->sort_order,
            ])->toArray(),
            'plans' => $problemSolving->plans->map(fn ($p) => [
                'id' => $p->id,
                'plan_number' => $p->plan_number,
                'action_plan' => $p->action_plan,
                'reflection' => $p->reflection,
                'created_at' => $p->created_at->format(DATE_ATOM),
                'updated_at' => $p->updated_at->format(DATE_ATOM),
            ])->toArray(),
            'tags' => $problemSolving->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $problemSolving->tags->pluck('id')->toArray(),
            'created_at' => $problemSolving->created_at->format(DATE_ATOM),
            'updated_at' => $problemSolving->updated_at->format(DATE_ATOM),
        ];
    }

    /**
     * 問題解決をCSV形式でエクスポート
     */
    public function exportCsv(SearchRequest $request, ExportProblemSolvingCsvUseCase $exportUseCase): StreamedResponse
    {
        $criteria = $request->toSearchCriteriaData();

        return $exportUseCase->handle($criteria);
    }
}

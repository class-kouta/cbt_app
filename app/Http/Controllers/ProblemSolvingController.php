<?php

namespace App\Http\Controllers;

use App\Application\DTO\ProblemSolvingData;
use App\Application\DTO\ProblemSolvingPlanData;
use App\Application\UseCase\ProblemSolving\CreateProblemSolvingUseCase;
use App\Application\UseCase\ProblemSolving\UpdateProblemSolvingUseCase;
use App\Application\UseCase\ProblemSolving\DeleteProblemSolvingUseCase;
use App\Application\UseCase\ProblemSolving\ExportProblemSolvingCsvUseCase;
use App\Application\UseCase\ProblemSolving\AddPlanUseCase;
use App\Application\UseCase\ProblemSolving\UpdatePlanUseCase;
use App\Application\UseCase\ProblemSolving\DeletePlanUseCase;
use App\Application\UseCase\ProblemSolving\SearchPlanUseCase;
use App\Application\UseCase\ProblemSolving\SearchProblemSolvingUseCase;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\ProblemSolving\SearchPlanRequest;
use App\Http\Requests\ProblemSolving\CreateProblemSolvingRequest;
use App\Http\Requests\ProblemSolving\UpdateProblemSolvingRequest;
use App\Http\Requests\ProblemSolving\AddPlanRequest;
use App\Http\Requests\ProblemSolving\UpdatePlanRequest;
use App\Infrastructure\Database\Models\ProblemSolving;
use App\Infrastructure\Database\Models\ProblemSolvingPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
        $problemSolving->load(['plans', 'tags']);
        return response()->json($this->formatProblemSolving($problemSolving));
    }

    /**
     * 計画一覧を取得（全件、作成日時降順）
     * 実行計画が入力されている計画のみを対象とする
     * キーワード検索・改善レベル範囲検索に対応
     */
    public function plans(SearchPlanRequest $request, SearchPlanUseCase $searchPlanUseCase): JsonResponse
    {
        $criteria = $request->toPlanSearchCriteriaData();
        $plans = $searchPlanUseCase->handle($criteria);

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
        $model = ProblemSolving::with('tags')->where('member_id', (int) Auth::id())->findOrFail($problemSolvingEntity->getId());
        if (!empty($tagIds)) {
            $model->tags()->sync($tagIds);
            $model->load('tags');
        }

        return response()->json([
            'id' => $problemSolvingEntity->getId(),
            'problem_situation' => $problemSolvingEntity->getProblemSituation(),
            'improved_image' => $problemSolvingEntity->getImprovedImage(),
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
            'plans' => array_map(fn ($p) => [
                'id' => $p->getId(),
                'plan_number' => $p->getPlanNumber(),
                'action_plan' => $p->getActionPlan(),
                'reflection' => $p->getReflection(),
                'improvement_level' => $p->getImprovementLevel(),
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
     * 計画を追加
     */
    public function addPlan(AddPlanRequest $request, ProblemSolving $problemSolving, AddPlanUseCase $addPlan): JsonResponse
    {
        $data = new ProblemSolvingPlanData(
            actionPlan: $request->filled('action_plan') ? (string) $request->string('action_plan') : null,
            reflection: $request->filled('reflection') ? (string) $request->string('reflection') : null,
            improvementLevel: $request->filled('improvement_level') ? (int) $request->integer('improvement_level') : null
        );

        $plan = $addPlan->handle($problemSolving->id, $data);

        return response()->json([
            'id' => $plan->getId(),
            'problem_solving_id' => $plan->getProblemSolvingId(),
            'plan_number' => $plan->getPlanNumber(),
            'action_plan' => $plan->getActionPlan(),
            'reflection' => $plan->getReflection(),
            'improvement_level' => $plan->getImprovementLevel(),
            'created_at' => $plan->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $plan->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * 計画を更新
     */
    public function updatePlan(UpdatePlanRequest $request, ProblemSolving $problemSolving, ProblemSolvingPlan $plan, UpdatePlanUseCase $updatePlan): JsonResponse
    {
        if ($plan->problem_solving_id !== $problemSolving->id) {
            abort(404);
        }

        $data = new ProblemSolvingPlanData(
            actionPlan: $request->filled('action_plan') ? (string) $request->string('action_plan') : null,
            reflection: $request->filled('reflection') ? (string) $request->string('reflection') : null,
            improvementLevel: $request->filled('improvement_level') ? (int) $request->integer('improvement_level') : null
        );

        $updated = $updatePlan->handle($plan->id, $data);

        return response()->json([
            'id' => $updated->getId(),
            'problem_solving_id' => $updated->getProblemSolvingId(),
            'plan_number' => $updated->getPlanNumber(),
            'action_plan' => $updated->getActionPlan(),
            'reflection' => $updated->getReflection(),
            'improvement_level' => $updated->getImprovementLevel(),
            'created_at' => $updated->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updated->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * 計画を削除
     */
    public function deletePlan(ProblemSolving $problemSolving, ProblemSolvingPlan $plan, DeletePlanUseCase $deletePlan): JsonResponse
    {
        if ($plan->problem_solving_id !== $problemSolving->id) {
            abort(404);
        }

        $deletePlan->handle($plan->id);

        return response()->json(null, 204);
    }

    /**
     * 振り返り未記入かつ作成から1週間以上経過した実行計画が存在するかチェック
     */
    public function hasOverdueReflection(): JsonResponse
    {
        $oneWeekAgo = now()->subWeek();

        $exists = ProblemSolvingPlan::whereNotNull('action_plan')
            ->where('action_plan', '!=', '')
            ->where(function ($query) {
                $query->whereNull('reflection')
                    ->orWhere('reflection', '');
            })
            ->whereHas('problemSolving', fn ($q) => $q->where('member_id', (int) Auth::id()))
            ->where('created_at', '<=', $oneWeekAgo)
            ->exists();

        return response()->json(['has_overdue' => $exists]);
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
            'plans' => $problemSolving->plans->map(fn ($p) => [
                'id' => $p->id,
                'plan_number' => $p->plan_number,
                'action_plan' => $p->action_plan,
                'reflection' => $p->reflection,
                'improvement_level' => $p->improvement_level,
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

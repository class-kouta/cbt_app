<?php

namespace App\Http\Controllers;

use App\Application\DTO\ProblemSolvingData;
use App\Application\DTO\ProblemSolvingSolutionData;
use App\Application\UseCase\ProblemSolving\CreateProblemSolvingUseCase;
use App\Application\UseCase\ProblemSolving\UpdateProblemSolvingUseCase;
use App\Application\UseCase\ProblemSolving\DeleteProblemSolvingUseCase;
use App\Application\UseCase\ProblemSolving\AddSolutionUseCase;
use App\Application\UseCase\ProblemSolving\UpdateSolutionUseCase;
use App\Application\UseCase\ProblemSolving\DeleteSolutionUseCase;
use App\Http\Requests\ProblemSolving\CreateProblemSolvingRequest;
use App\Http\Requests\ProblemSolving\UpdateProblemSolvingRequest;
use App\Http\Requests\ProblemSolving\AddSolutionRequest;
use App\Http\Requests\ProblemSolving\UpdateSolutionRequest;
use App\Infrastructure\Database\Models\ProblemSolving;
use App\Infrastructure\Database\Models\ProblemSolvingSolution;
use Illuminate\Http\JsonResponse;

class ProblemSolvingController extends Controller
{
    /**
     * 問題解決一覧を取得（作成日時降順）
     */
    public function index(): JsonResponse
    {
        $problemSolvings = ProblemSolving::with('solutions')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($item) => $this->formatProblemSolving($item));

        return response()->json($problemSolvings);
    }

    /**
     * 問題解決詳細を取得
     */
    public function show(ProblemSolving $problemSolving): JsonResponse
    {
        $problemSolving->load('solutions');
        return response()->json($this->formatProblemSolving($problemSolving));
    }

    /**
     * 問題解決を作成
     */
    public function store(CreateProblemSolvingRequest $request, CreateProblemSolvingUseCase $createProblemSolving): JsonResponse
    {
        $data = new ProblemSolvingData(
            problemSituation: (string) $request->string('problem_situation'),
            improvedImage: $request->filled('improved_image') ? (string) $request->string('improved_image') : null,
            actionPlan: $request->filled('action_plan') ? (string) $request->string('action_plan') : null,
            reflection: $request->filled('reflection') ? (string) $request->string('reflection') : null
        );

        $problemSolving = $createProblemSolving->handle($data);

        return response()->json([
            'id' => $problemSolving->getId(),
            'problem_situation' => $problemSolving->getProblemSituation(),
            'improved_image' => $problemSolving->getImprovedImage(),
            'action_plan' => $problemSolving->getActionPlan(),
            'reflection' => $problemSolving->getReflection(),
            'solutions' => [],
            'created_at' => $problemSolving->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $problemSolving->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * 問題解決を更新
     */
    public function update(UpdateProblemSolvingRequest $request, ProblemSolving $problemSolving, UpdateProblemSolvingUseCase $updateProblemSolving): JsonResponse
    {
        $data = new ProblemSolvingData(
            problemSituation: (string) $request->string('problem_situation'),
            improvedImage: $request->filled('improved_image') ? (string) $request->string('improved_image') : null,
            actionPlan: $request->filled('action_plan') ? (string) $request->string('action_plan') : null,
            reflection: $request->filled('reflection') ? (string) $request->string('reflection') : null
        );

        $updated = $updateProblemSolving->handle($problemSolving->id, $data);

        return response()->json([
            'id' => $updated->getId(),
            'problem_situation' => $updated->getProblemSituation(),
            'improved_image' => $updated->getImprovedImage(),
            'action_plan' => $updated->getActionPlan(),
            'reflection' => $updated->getReflection(),
            'solutions' => array_map(fn ($s) => [
                'id' => $s->getId(),
                'content' => $s->getContent(),
                'effectiveness' => $s->getEffectiveness(),
                'feasibility' => $s->getFeasibility(),
                'sort_order' => $s->getSortOrder(),
            ], $updated->getSolutions()),
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
     * 問題解決をJSON形式にフォーマット
     */
    private function formatProblemSolving(ProblemSolving $problemSolving): array
    {
        return [
            'id' => $problemSolving->id,
            'problem_situation' => $problemSolving->problem_situation,
            'improved_image' => $problemSolving->improved_image,
            'action_plan' => $problemSolving->action_plan,
            'reflection' => $problemSolving->reflection,
            'solutions' => $problemSolving->solutions->map(fn ($s) => [
                'id' => $s->id,
                'content' => $s->content,
                'effectiveness' => $s->effectiveness,
                'feasibility' => $s->feasibility,
                'sort_order' => $s->sort_order,
            ])->toArray(),
            'created_at' => $problemSolving->created_at->format(DATE_ATOM),
            'updated_at' => $problemSolving->updated_at->format(DATE_ATOM),
        ];
    }
}

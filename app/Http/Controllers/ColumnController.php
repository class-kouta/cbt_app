<?php

namespace App\Http\Controllers;

use App\Application\DTO\ColumnData;
use App\Application\UseCase\Column\CreateColumnUseCase;
use App\Application\UseCase\Column\DeleteColumnUseCase;
use App\Application\UseCase\Column\UpdateColumnUseCase;
use App\Http\Requests\Column\CreateColumnRequest;
use App\Http\Requests\Column\UpdateColumnRequest;
use App\Infrastructure\Database\Models\Column;
use Illuminate\Http\JsonResponse;

class ColumnController extends Controller
{
    /**
     * コラム一覧を取得（作成日時降順）
     */
    public function index(): JsonResponse
    {
        $columns = Column::orderByDesc('created_at')
            ->get()
            ->map(function ($column) {
                return [
                    'id' => $column->id,
                    'situation' => $column->situation,
                    'mood' => $column->mood,
                    'automatic_thought' => $column->automatic_thought,
                    'evidence' => $column->evidence,
                    'counter_evidence' => $column->counter_evidence,
                    'adaptive_thought' => $column->adaptive_thought,
                    'current_mood' => $column->current_mood,
                    'notes' => $column->notes,
                    'created_at' => $column->created_at->format(DATE_ATOM),
                    'updated_at' => $column->updated_at->format(DATE_ATOM),
                ];
            });

        return response()->json($columns);
    }

    /**
     * 適応的思考が入力されているコラム一覧を取得（作成日時降順）
     */
    public function adaptiveThoughts(): JsonResponse
    {
        $columns = Column::whereNotNull('adaptive_thought')
            ->where('adaptive_thought', '!=', '')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($column) {
                return [
                    'id' => $column->id,
                    'situation' => $column->situation,
                    'adaptive_thought' => $column->adaptive_thought,
                    'created_at' => $column->created_at->format(DATE_ATOM),
                    'updated_at' => $column->updated_at->format(DATE_ATOM),
                ];
            });

        return response()->json($columns);
    }

    /**
     * コラム詳細を取得
     */
    public function show(Column $column): JsonResponse
    {
        return response()->json([
            'id' => $column->id,
            'situation' => $column->situation,
            'mood' => $column->mood,
            'automatic_thought' => $column->automatic_thought,
            'evidence' => $column->evidence,
            'counter_evidence' => $column->counter_evidence,
            'adaptive_thought' => $column->adaptive_thought,
            'current_mood' => $column->current_mood,
            'notes' => $column->notes,
            'created_at' => $column->created_at->format(DATE_ATOM),
            'updated_at' => $column->updated_at->format(DATE_ATOM),
        ]);
    }

    /**
     * コラムを作成
     */
    public function store(CreateColumnRequest $request, CreateColumnUseCase $createColumn): JsonResponse
    {
        $data = new ColumnData(
            situation: (string) $request->string('situation'),
            mood: $request->has('mood') && $request->filled('mood') ? (string) $request->string('mood') : null,
            automaticThought: $request->has('automatic_thought') && $request->filled('automatic_thought') ? (string) $request->string('automatic_thought') : null,
            evidence: $request->has('evidence') && $request->filled('evidence') ? (string) $request->string('evidence') : null,
            counterEvidence: $request->has('counter_evidence') && $request->filled('counter_evidence') ? (string) $request->string('counter_evidence') : null,
            adaptiveThought: $request->has('adaptive_thought') && $request->filled('adaptive_thought') ? (string) $request->string('adaptive_thought') : null,
            currentMood: $request->has('current_mood') && $request->filled('current_mood') ? (string) $request->string('current_mood') : null,
            notes: $request->has('notes') && $request->filled('notes') ? (string) $request->string('notes') : null
        );

        $column = $createColumn->handle($data);

        return response()->json([
            'id' => $column->getId(),
            'situation' => $column->getSituation(),
            'mood' => $column->getMood(),
            'automatic_thought' => $column->getAutomaticThought(),
            'evidence' => $column->getEvidence(),
            'counter_evidence' => $column->getCounterEvidence(),
            'adaptive_thought' => $column->getAdaptiveThought(),
            'current_mood' => $column->getCurrentMood(),
            'notes' => $column->getNotes(),
            'created_at' => $column->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $column->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * コラムを更新
     */
    public function update(UpdateColumnRequest $request, Column $column, UpdateColumnUseCase $updateColumn): JsonResponse
    {
        $data = new ColumnData(
            situation: (string) $request->string('situation'),
            mood: $request->has('mood') && $request->filled('mood') ? (string) $request->string('mood') : null,
            automaticThought: $request->has('automatic_thought') && $request->filled('automatic_thought') ? (string) $request->string('automatic_thought') : null,
            evidence: $request->has('evidence') && $request->filled('evidence') ? (string) $request->string('evidence') : null,
            counterEvidence: $request->has('counter_evidence') && $request->filled('counter_evidence') ? (string) $request->string('counter_evidence') : null,
            adaptiveThought: $request->has('adaptive_thought') && $request->filled('adaptive_thought') ? (string) $request->string('adaptive_thought') : null,
            currentMood: $request->has('current_mood') && $request->filled('current_mood') ? (string) $request->string('current_mood') : null,
            notes: $request->has('notes') && $request->filled('notes') ? (string) $request->string('notes') : null
        );

        $updatedColumn = $updateColumn->handle($column->id, $data);

        return response()->json([
            'id' => $updatedColumn->getId(),
            'situation' => $updatedColumn->getSituation(),
            'mood' => $updatedColumn->getMood(),
            'automatic_thought' => $updatedColumn->getAutomaticThought(),
            'evidence' => $updatedColumn->getEvidence(),
            'counter_evidence' => $updatedColumn->getCounterEvidence(),
            'adaptive_thought' => $updatedColumn->getAdaptiveThought(),
            'current_mood' => $updatedColumn->getCurrentMood(),
            'notes' => $updatedColumn->getNotes(),
            'created_at' => $updatedColumn->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedColumn->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * コラムを削除
     */
    public function destroy(Column $column, DeleteColumnUseCase $deleteColumn): JsonResponse
    {
        $deleteColumn->handle($column->id);

        return response()->json(null, 204);
    }
}

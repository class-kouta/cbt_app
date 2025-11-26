<?php

namespace App\Http\Controllers;

use App\Application\DTO\ColumnData;
use App\Application\UseCase\Column\CreateColumnUseCase;
use App\Application\UseCase\Column\DeleteColumnUseCase;
use App\Http\Requests\Column\CreateColumnRequest;
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
            mood: (string) $request->string('mood'),
            automaticThought: (string) $request->string('automatic_thought'),
            evidence: (string) $request->string('evidence'),
            counterEvidence: (string) $request->string('counter_evidence'),
            adaptiveThought: (string) $request->string('adaptive_thought'),
            currentMood: (string) $request->string('current_mood')
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
            'created_at' => $column->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $column->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
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

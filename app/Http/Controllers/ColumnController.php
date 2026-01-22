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
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    /**
     * コラム一覧を取得（作成日時降順）
     * キーワード検索とタグ検索に対応
     */
    public function index(Request $request): JsonResponse
    {
        $query = Column::with('tags');

        // キーワード検索（状況、感情、自動思考、根拠、反証、適応的思考、現在の感情、メモで部分一致）
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('situation', 'like', "%{$keyword}%")
                    ->orWhere('mood', 'like', "%{$keyword}%")
                    ->orWhere('automatic_thought', 'like', "%{$keyword}%")
                    ->orWhere('evidence', 'like', "%{$keyword}%")
                    ->orWhere('counter_evidence', 'like', "%{$keyword}%")
                    ->orWhere('adaptive_thought', 'like', "%{$keyword}%")
                    ->orWhere('current_mood', 'like', "%{$keyword}%")
                    ->orWhere('notes', 'like', "%{$keyword}%");
            });
        }

        // タグ検索（指定されたタグIDのいずれかに紐づくアイテム）
        if ($request->filled('tag_ids')) {
            $tagIds = $request->input('tag_ids');
            if (is_array($tagIds) && count($tagIds) > 0) {
                $query->whereHas('tags', function ($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                });
            }
        }

        $columns = $query->orderByDesc('created_at')
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
                    'tags' => $column->tags->map(fn ($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ])->toArray(),
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
        $column->load('tags');

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
            'tags' => $column->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $column->tags->pluck('id')->toArray(),
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

        $columnEntity = $createColumn->handle($data);

        // タグの紐付け
        $tagIds = $request->input('tag_ids', []);
        $model = Column::with('tags')->find($columnEntity->getId());
        if (!empty($tagIds)) {
            $model->tags()->sync($tagIds);
            $model->load('tags');
        }

        return response()->json([
            'id' => $columnEntity->getId(),
            'situation' => $columnEntity->getSituation(),
            'mood' => $columnEntity->getMood(),
            'automatic_thought' => $columnEntity->getAutomaticThought(),
            'evidence' => $columnEntity->getEvidence(),
            'counter_evidence' => $columnEntity->getCounterEvidence(),
            'adaptive_thought' => $columnEntity->getAdaptiveThought(),
            'current_mood' => $columnEntity->getCurrentMood(),
            'notes' => $columnEntity->getNotes(),
            'tags' => $model->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $model->tags->pluck('id')->toArray(),
            'created_at' => $columnEntity->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $columnEntity->getUpdatedAt()->format(DATE_ATOM),
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

        // タグの同期
        $tagIds = $request->input('tag_ids', []);
        $column->tags()->sync($tagIds);
        $column->load('tags');

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
            'tags' => $column->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $column->tags->pluck('id')->toArray(),
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

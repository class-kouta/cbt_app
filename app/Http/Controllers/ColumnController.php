<?php

namespace App\Http\Controllers;

use App\Application\UseCase\Column\CreateColumnUseCase;
use App\Application\UseCase\Column\DeleteColumnUseCase;
use App\Application\UseCase\Column\ExportColumnCsvUseCase;
use App\Application\UseCase\Column\SearchColumnUseCase;
use App\Application\UseCase\Column\UpdateColumnUseCase;
use App\Http\Requests\Column\CreateColumnRequest;
use App\Http\Requests\Column\UpdateColumnRequest;
use App\Http\Requests\Common\SearchRequest;
use App\Infrastructure\Database\Models\Column;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ColumnController extends Controller
{
    /**
     * コラム一覧を取得（作成日時降順）
     * キーワード検索とタグ検索に対応
     */
    public function index(SearchRequest $request, SearchColumnUseCase $searchUseCase): JsonResponse
    {
        $criteria = $request->toSearchCriteriaData();
        $columns = $searchUseCase->handle($criteria);

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
            'stressor_and_response_id' => $column->stressor_and_response_id,
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
        $result = $createColumn->handle($request->toColumnData());

        return response()->json($result, 201);
    }

    /**
     * コラムを更新
     */
    public function update(UpdateColumnRequest $request, Column $column, UpdateColumnUseCase $updateColumn): JsonResponse
    {
        $data = $request->toColumnData($column->stressor_and_response_id);
        $result = $updateColumn->handle($column->id, $data);

        return response()->json($result);
    }

    /**
     * コラムを削除
     */
    public function destroy(Column $column, DeleteColumnUseCase $deleteColumn): JsonResponse
    {
        $deleteColumn->handle($column->id);

        return response()->json(null, 204);
    }

    /**
     * コラムをCSV形式でエクスポート
     */
    public function exportCsv(SearchRequest $request, ExportColumnCsvUseCase $exportUseCase): StreamedResponse
    {
        $criteria = $request->toSearchCriteriaData();

        return $exportUseCase->handle($criteria);
    }
}

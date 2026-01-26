<?php

namespace App\Http\Controllers;

use App\Application\UseCase\AnxietyDiary\CreateAnxietyDiaryUseCase;
use App\Application\UseCase\AnxietyDiary\DeleteAnxietyDiaryUseCase;
use App\Application\UseCase\AnxietyDiary\ExportAnxietyDiaryCsvUseCase;
use App\Application\UseCase\AnxietyDiary\SearchAnxietyDiaryUseCase;
use App\Application\UseCase\AnxietyDiary\UpdateAnxietyDiaryUseCase;
use App\Http\Requests\AnxietyDiary\CreateAnxietyDiaryRequest;
use App\Http\Requests\AnxietyDiary\UpdateAnxietyDiaryRequest;
use App\Http\Requests\Common\SearchRequest;
use App\Infrastructure\Database\Models\AnxietyDiary;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnxietyDiaryController extends Controller
{
    /**
     * 不安日記一覧を取得（作成日時降順）
     * キーワード検索とページネーションに対応
     */
    public function index(SearchRequest $request, SearchAnxietyDiaryUseCase $searchUseCase): JsonResponse
    {
        $criteria = $request->toSearchCriteriaData();
        $result = $searchUseCase->handle($criteria);

        return response()->json($result);
    }

    /**
     * 不安日記詳細を取得
     */
    public function show(AnxietyDiary $anxietyDiary): JsonResponse
    {
        return response()->json([
            'id' => $anxietyDiary->id,
            'situation' => $anxietyDiary->situation,
            'anxiety_thought' => $anxietyDiary->anxiety_thought,
            'actual_outcome' => $anxietyDiary->actual_outcome,
            'stressor_and_response_id' => $anxietyDiary->stressor_and_response_id,
            'created_at' => $anxietyDiary->created_at->format(DATE_ATOM),
            'updated_at' => $anxietyDiary->updated_at->format(DATE_ATOM),
        ]);
    }

    /**
     * 不安日記を作成
     */
    public function store(CreateAnxietyDiaryRequest $request, CreateAnxietyDiaryUseCase $createUseCase): JsonResponse
    {
        $item = $createUseCase->handle($request->toAnxietyDiaryData());

        return response()->json([
            'id' => $item->getId(),
            'situation' => $item->getSituation(),
            'anxiety_thought' => $item->getAnxietyThought(),
            'actual_outcome' => $item->getActualOutcome(),
            'stressor_and_response_id' => $item->getStressorAndResponseId(),
            'created_at' => $item->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * 不安日記を更新
     */
    public function update(UpdateAnxietyDiaryRequest $request, AnxietyDiary $anxietyDiary, UpdateAnxietyDiaryUseCase $updateUseCase): JsonResponse
    {
        $data = $request->toAnxietyDiaryData($anxietyDiary->stressor_and_response_id);
        $updatedItem = $updateUseCase->handle($anxietyDiary->id, $data);

        return response()->json([
            'id' => $updatedItem->getId(),
            'situation' => $updatedItem->getSituation(),
            'anxiety_thought' => $updatedItem->getAnxietyThought(),
            'actual_outcome' => $updatedItem->getActualOutcome(),
            'stressor_and_response_id' => $updatedItem->getStressorAndResponseId(),
            'created_at' => $updatedItem->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedItem->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * 不安日記を削除
     */
    public function destroy(AnxietyDiary $anxietyDiary, DeleteAnxietyDiaryUseCase $deleteUseCase): JsonResponse
    {
        $deleteUseCase->handle($anxietyDiary->id);

        return response()->json(null, 204);
    }

    /**
     * 不安日記をCSV形式でエクスポート
     */
    public function exportCsv(SearchRequest $request, ExportAnxietyDiaryCsvUseCase $exportUseCase): StreamedResponse
    {
        $criteria = $request->toSearchCriteriaData();

        return $exportUseCase->handle($criteria);
    }
}

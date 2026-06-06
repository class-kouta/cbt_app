<?php

namespace App\Http\Controllers;

use App\Application\DTO\ConditionCheckData;
use App\Application\UseCase\ConditionCheck\CreateConditionCheckUseCase;
use App\Application\UseCase\ConditionCheck\DeleteConditionCheckUseCase;
use App\Application\UseCase\ConditionCheck\SearchConditionCheckUseCase;
use App\Application\UseCase\ConditionCheck\UpdateConditionCheckUseCase;
use App\Http\Requests\ConditionCheck\CreateConditionCheckRequest;
use App\Http\Requests\ConditionCheck\UpdateConditionCheckRequest;
use App\Infrastructure\Database\Models\ConditionCheck;
use Illuminate\Http\JsonResponse;

class ConditionCheckController extends Controller
{
    /**
     * コンディションチェック一覧を取得（作成日時降順）
     */
    public function index(SearchConditionCheckUseCase $searchConditionCheck): JsonResponse
    {
        $items = collect($searchConditionCheck->handle())
            ->map(fn ($item) => $this->toArray($item));

        return response()->json($items);
    }

    /**
     * コンディションチェック詳細を取得
     */
    public function show(ConditionCheck $conditionCheck): JsonResponse
    {
        return response()->json([
            'id' => $conditionCheck->id,
            'mood' => $conditionCheck->mood,
            'fatigue' => $conditionCheck->fatigue,
            'anxiety' => $conditionCheck->anxiety,
            'sleepiness' => $conditionCheck->sleepiness,
            'physical_condition' => $conditionCheck->physical_condition,
            'memo' => $conditionCheck->memo,
            'created_at' => $conditionCheck->created_at?->format(DATE_ATOM),
            'updated_at' => $conditionCheck->updated_at?->format(DATE_ATOM),
        ]);
    }

    /**
     * コンディションチェックを作成
     */
    public function store(
        CreateConditionCheckRequest $request,
        CreateConditionCheckUseCase $createConditionCheck,
    ): JsonResponse {
        $data = $this->toData($request);

        $item = $createConditionCheck->handle($data);

        return response()->json($this->toArray($item), 201);
    }

    /**
     * コンディションチェックを更新
     */
    public function update(
        UpdateConditionCheckRequest $request,
        ConditionCheck $conditionCheck,
        UpdateConditionCheckUseCase $updateConditionCheck,
    ): JsonResponse {
        $data = $this->toData($request);

        $item = $updateConditionCheck->handle($conditionCheck->id, $data);

        return response()->json($this->toArray($item));
    }

    /**
     * コンディションチェックを削除
     */
    public function destroy(
        ConditionCheck $conditionCheck,
        DeleteConditionCheckUseCase $deleteConditionCheck,
    ): JsonResponse {
        $deleteConditionCheck->handle($conditionCheck->id);

        return response()->json(null, 204);
    }

    private function toData(CreateConditionCheckRequest $request): ConditionCheckData
    {
        $memo = $request->input('memo');

        return new ConditionCheckData(
            mood: (int) $request->input('mood'),
            fatigue: (int) $request->input('fatigue'),
            anxiety: (int) $request->input('anxiety'),
            sleepiness: (int) $request->input('sleepiness'),
            physicalCondition: (int) $request->input('physical_condition'),
            memo: is_string($memo) && $memo !== '' ? $memo : null,
        );
    }

    /**
     * @param \App\Domain\Entity\ConditionCheck $item
     * @return array<string, mixed>
     */
    private function toArray($item): array
    {
        return [
            'id' => $item->getId(),
            'mood' => $item->getMood(),
            'fatigue' => $item->getFatigue(),
            'anxiety' => $item->getAnxiety(),
            'sleepiness' => $item->getSleepiness(),
            'physical_condition' => $item->getPhysicalCondition(),
            'memo' => $item->getMemo(),
            'created_at' => $item->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}

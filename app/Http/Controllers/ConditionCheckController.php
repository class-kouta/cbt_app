<?php

namespace App\Http\Controllers;

use App\Application\DTO\ConditionCheckData;
use App\Application\UseCase\ConditionCheck\CreateConditionCheckUseCase;
use App\Application\UseCase\ConditionCheck\DeleteConditionCheckUseCase;
use App\Application\UseCase\ConditionCheck\FindConditionCheckUseCase;
use App\Application\UseCase\ConditionCheck\SearchConditionCheckUseCase;
use App\Application\UseCase\ConditionCheck\UpdateConditionCheckUseCase;
use App\Enums\ConditionCheckRating;
use App\Http\Requests\ConditionCheck\ConditionCheckIndexRequest;
use App\Http\Requests\ConditionCheck\CreateConditionCheckRequest;
use App\Http\Requests\ConditionCheck\UpdateConditionCheckRequest;
use DomainException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class ConditionCheckController extends Controller
{
    /**
     * コンディションチェック一覧を取得（作成日時降順）
     */
    public function index(
        ConditionCheckIndexRequest $request,
        SearchConditionCheckUseCase $searchConditionCheck,
    ): JsonResponse {
        $criteria = $request->toSearchCriteriaData();

        return response()->json($searchConditionCheck->handle($criteria));
    }

    /**
     * コンディションチェック詳細を取得
     */
    public function show(
        int $conditionCheck,
        FindConditionCheckUseCase $findConditionCheck,
    ): JsonResponse {
        try {
            $item = $findConditionCheck->handle($conditionCheck);
        } catch (DomainException) {
            abort(404);
        }

        return response()->json($this->toArray($item));
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
        int $conditionCheck,
        UpdateConditionCheckUseCase $updateConditionCheck,
    ): JsonResponse {
        $data = $this->toData($request);

        try {
            $item = $updateConditionCheck->handle($conditionCheck, $data);
        } catch (DomainException) {
            abort(404);
        }

        return response()->json($this->toArray($item));
    }

    /**
     * コンディションチェックを削除
     */
    public function destroy(
        int $conditionCheck,
        DeleteConditionCheckUseCase $deleteConditionCheck,
    ): JsonResponse {
        try {
            $deleteConditionCheck->handle($conditionCheck);
        } catch (DomainException) {
            abort(404);
        }

        return response()->json(null, 204);
    }

    private function toData(FormRequest $request): ConditionCheckData
    {
        $validated = $request->validated();
        $memo = $validated['memo'] ?? null;

        return new ConditionCheckData(
            mood: $this->ratingValue($validated['mood']),
            fatigue: $this->ratingValue($validated['fatigue']),
            anxiety: $this->ratingValue($validated['anxiety']),
            sleepiness: $this->ratingValue($validated['sleepiness']),
            physicalCondition: $this->ratingValue($validated['physical_condition']),
            memo: is_string($memo) && $memo !== '' ? $memo : null,
        );
    }

    private function ratingValue(mixed $value): int
    {
        return $value instanceof ConditionCheckRating ? $value->value : (int) $value;
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

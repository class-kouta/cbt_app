<?php

namespace App\Http\Controllers;

use App\Application\DTO\StressPersonEncyclopediaData;
use App\Application\UseCase\StressPersonEncyclopedia\CreateStressPersonEncyclopediaUseCase;
use App\Application\UseCase\StressPersonEncyclopedia\DeleteStressPersonEncyclopediaUseCase;
use App\Application\UseCase\StressPersonEncyclopedia\FindStressPersonEncyclopediaUseCase;
use App\Application\UseCase\StressPersonEncyclopedia\PresentStressPersonEncyclopediaUseCase;
use App\Application\UseCase\StressPersonEncyclopedia\SearchStressPersonEncyclopediaUseCase;
use App\Application\UseCase\StressPersonEncyclopedia\UpdateStressPersonEncyclopediaUseCase;
use App\Http\Requests\StressPersonEncyclopedia\CreateStressPersonEncyclopediaRequest;
use App\Http\Requests\StressPersonEncyclopedia\UpdateStressPersonEncyclopediaRequest;
use DomainException;
use Illuminate\Http\JsonResponse;

class StressPersonEncyclopediaController extends Controller
{
    /**
     * ストレス人物図鑑一覧を取得（作成日時降順）
     */
    public function index(SearchStressPersonEncyclopediaUseCase $searchStressPersonEncyclopedia): JsonResponse
    {
        return response()->json($searchStressPersonEncyclopedia->handle());
    }

    /**
     * ストレス人物図鑑詳細を取得
     */
    public function show(
        int $stressPersonEncyclopedia,
        FindStressPersonEncyclopediaUseCase $findStressPersonEncyclopedia,
        PresentStressPersonEncyclopediaUseCase $presentStressPersonEncyclopedia,
    ): JsonResponse {
        try {
            $encyclopedia = $findStressPersonEncyclopedia->handle($stressPersonEncyclopedia);
        } catch (DomainException) {
            abort(404);
        }

        return response()->json($presentStressPersonEncyclopedia->handle($encyclopedia));
    }

    /**
     * ストレス人物図鑑を作成
     */
    public function store(
        CreateStressPersonEncyclopediaRequest $request,
        CreateStressPersonEncyclopediaUseCase $createStressPersonEncyclopedia,
        PresentStressPersonEncyclopediaUseCase $presentStressPersonEncyclopedia,
    ): JsonResponse {
        $data = $this->toData($request);

        $encyclopedia = $createStressPersonEncyclopedia->handle($data);

        return response()->json($presentStressPersonEncyclopedia->handle($encyclopedia), 201);
    }

    /**
     * ストレス人物図鑑を更新
     */
    public function update(
        UpdateStressPersonEncyclopediaRequest $request,
        int $stressPersonEncyclopedia,
        UpdateStressPersonEncyclopediaUseCase $updateStressPersonEncyclopedia,
        PresentStressPersonEncyclopediaUseCase $presentStressPersonEncyclopedia,
    ): JsonResponse {
        $data = $this->toData($request);

        try {
            $encyclopedia = $updateStressPersonEncyclopedia->handle($stressPersonEncyclopedia, $data);
        } catch (DomainException) {
            abort(404);
        }

        return response()->json($presentStressPersonEncyclopedia->handle($encyclopedia));
    }

    /**
     * ストレス人物図鑑を削除
     */
    public function destroy(
        int $stressPersonEncyclopedia,
        DeleteStressPersonEncyclopediaUseCase $deleteStressPersonEncyclopedia,
    ): JsonResponse {
        try {
            $deleteStressPersonEncyclopedia->handle($stressPersonEncyclopedia);
        } catch (DomainException) {
            abort(404);
        }

        return response()->json(null, 204);
    }

    private function toData(CreateStressPersonEncyclopediaRequest $request): StressPersonEncyclopediaData
    {
        $validated = $request->validated();

        return new StressPersonEncyclopediaData(
            name: trim($validated['name']),
            relationship: $this->nullableString($validated['relationship'] ?? null),
            difficultTraits: $this->nullableString($validated['difficult_traits'] ?? null),
            myReaction: $this->nullableString($validated['my_reaction'] ?? null),
            copingStrategy: $this->nullableString($validated['coping_strategy'] ?? null),
            notes: $this->nullableString($validated['notes'] ?? null),
        );
    }

    private function nullableString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}

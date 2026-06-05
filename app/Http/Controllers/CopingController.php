<?php

namespace App\Http\Controllers;

use App\Application\DTO\CopingData;
use App\Application\UseCase\Coping\CreateCopingUseCase;
use App\Application\UseCase\Coping\DeleteCopingUseCase;
use App\Application\UseCase\Coping\UpdateCopingUseCase;
use App\Domain\Repository\CopingRepositoryInterface;
use App\Http\Requests\Coping\CreateCopingRequest;
use App\Http\Requests\Coping\UpdateCopingRequest;
use App\Infrastructure\Database\Models\Coping;
use Illuminate\Http\JsonResponse;

class CopingController extends Controller
{
    /**
     * コーピング一覧を取得（ポイント高い順、同ポイントは作成日時降順）
     */
    public function index(CopingRepositoryInterface $copingRepository): JsonResponse
    {
        $copings = collect($copingRepository->findAllForMember((int) auth()->id()))
            ->map(function ($coping) {
                return [
                    'id' => $coping->getId(),
                    'content' => $coping->getContent(),
                    'point' => $coping->getPoint(),
                    'created_at' => $coping->getCreatedAt()->format(DATE_ATOM),
                    'updated_at' => $coping->getUpdatedAt()->format(DATE_ATOM),
                ];
            });

        return response()->json($copings);
    }

    /**
     * コーピングを作成
     */
    public function store(CreateCopingRequest $request, CreateCopingUseCase $createCoping): JsonResponse
    {
        $data = new CopingData(
            content: (string) $request->string('content')
        );

        $coping = $createCoping->handle($data);

        return response()->json([
            'id' => $coping->getId(),
            'content' => $coping->getContent(),
            'point' => $coping->getPoint(),
            'created_at' => $coping->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $coping->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * コーピングを更新
     */
    public function update(UpdateCopingRequest $request, Coping $coping, UpdateCopingUseCase $updateCoping): JsonResponse
    {
        $data = new CopingData(
            content: (string) $request->string('content'),
            point: $request->has('point') ? (int) $request->integer('point') : null
        );

        $updatedCoping = $updateCoping->handle($coping->id, $data);

        return response()->json([
            'id' => $updatedCoping->getId(),
            'content' => $updatedCoping->getContent(),
            'point' => $updatedCoping->getPoint(),
            'created_at' => $updatedCoping->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedCoping->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * コーピングを削除
     */
    public function destroy(Coping $coping, DeleteCopingUseCase $deleteCoping): JsonResponse
    {
        $deleteCoping->handle($coping->id);

        return response()->json(null, 204);
    }
}

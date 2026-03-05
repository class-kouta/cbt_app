<?php

namespace App\Http\Controllers;

use App\Application\DTO\ModeMapData;
use App\Application\UseCase\ModeMap\CreateModeMapUseCase;
use App\Application\UseCase\ModeMap\UpdateModeMapUseCase;
use App\Domain\Entity\ModeMap;
use App\Domain\Repository\ModeMapRepositoryInterface;
use App\Http\Requests\ModeMap\CreateModeMapRequest;
use App\Http\Requests\ModeMap\UpdateModeMapRequest;
use Illuminate\Http\JsonResponse;

class ModeMapController extends Controller
{
    public function show(ModeMapRepositoryInterface $repository): JsonResponse
    {
        $modeMap = $repository->findFirst();

        if ($modeMap === null) {
            return response()->json([
                'id' => null,
                'wounded_child_mode' => null,
                'hurtful_adult_mode' => null,
                'unacceptable_coping_mode' => null,
                'healthy_happy_child_mode' => null,
                'healthy_adult_mode' => null,
                'created_at' => null,
                'updated_at' => null,
            ]);
        }

        return $this->toResponse($modeMap);
    }

    public function store(CreateModeMapRequest $request, CreateModeMapUseCase $createModeMap): JsonResponse
    {
        $data = new ModeMapData(
            woundedChildMode: $request->input('wounded_child_mode'),
            hurtfulAdultMode: $request->input('hurtful_adult_mode'),
            unacceptableCopingMode: $request->input('unacceptable_coping_mode'),
            healthyHappyChildMode: $request->input('healthy_happy_child_mode'),
            healthyAdultMode: $request->input('healthy_adult_mode')
        );

        return $this->toResponse($createModeMap->handle($data), 201);
    }

    public function update(UpdateModeMapRequest $request, int $id, UpdateModeMapUseCase $updateModeMap): JsonResponse
    {
        $data = new ModeMapData(
            woundedChildMode: $request->input('wounded_child_mode'),
            hurtfulAdultMode: $request->input('hurtful_adult_mode'),
            unacceptableCopingMode: $request->input('unacceptable_coping_mode'),
            healthyHappyChildMode: $request->input('healthy_happy_child_mode'),
            healthyAdultMode: $request->input('healthy_adult_mode')
        );

        return $this->toResponse($updateModeMap->handle($id, $data));
    }

    private function toResponse(ModeMap $modeMap, int $status = 200): JsonResponse
    {
        return response()->json([
            'id' => $modeMap->getId(),
            'wounded_child_mode' => $modeMap->getWoundedChildMode(),
            'hurtful_adult_mode' => $modeMap->getHurtfulAdultMode(),
            'unacceptable_coping_mode' => $modeMap->getUnacceptableCopingMode(),
            'healthy_happy_child_mode' => $modeMap->getHealthyHappyChildMode(),
            'healthy_adult_mode' => $modeMap->getHealthyAdultMode(),
            'created_at' => $modeMap->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $modeMap->getUpdatedAt()->format(DATE_ATOM),
        ], $status);
    }
}

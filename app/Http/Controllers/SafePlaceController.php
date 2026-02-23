<?php

namespace App\Http\Controllers;

use App\Application\DTO\SafePlaceData;
use App\Application\UseCase\SafePlace\CreateSafePlaceUseCase;
use App\Application\UseCase\SafePlace\UpdateSafePlaceUseCase;
use App\Http\Requests\SafePlace\CreateSafePlaceRequest;
use App\Http\Requests\SafePlace\UpdateSafePlaceRequest;
use App\Infrastructure\Database\Models\SafePlace;
use Illuminate\Http\JsonResponse;

class SafePlaceController extends Controller
{
    public function show(): JsonResponse
    {
        $safePlace = SafePlace::first();

        if ($safePlace === null) {
            return response()->json([
                'id' => null,
                'safe_image' => null,
                'safe_something' => null,
                'created_at' => null,
                'updated_at' => null,
            ]);
        }

        return response()->json([
            'id' => $safePlace->id,
            'safe_image' => $safePlace->safe_image,
            'safe_something' => $safePlace->safe_something,
            'created_at' => $safePlace->created_at->format(DATE_ATOM),
            'updated_at' => $safePlace->updated_at->format(DATE_ATOM),
        ]);
    }

    public function store(CreateSafePlaceRequest $request, CreateSafePlaceUseCase $createSafePlace): JsonResponse
    {
        $data = new SafePlaceData(
            safeImage: $request->input('safe_image'),
            safeSomething: $request->input('safe_something')
        );
        $safePlace = $createSafePlace->handle($data);

        return response()->json([
            'id' => $safePlace->getId(),
            'safe_image' => $safePlace->getSafeImage(),
            'safe_something' => $safePlace->getSafeSomething(),
            'created_at' => $safePlace->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $safePlace->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    public function update(UpdateSafePlaceRequest $request, SafePlace $safePlace, UpdateSafePlaceUseCase $updateSafePlace): JsonResponse
    {
        $data = new SafePlaceData(
            safeImage: $request->input('safe_image'),
            safeSomething: $request->input('safe_something')
        );
        $entity = $updateSafePlace->handle($safePlace->id, $data);

        return response()->json([
            'id' => $entity->getId(),
            'safe_image' => $entity->getSafeImage(),
            'safe_something' => $entity->getSafeSomething(),
            'created_at' => $entity->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $entity->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }
}

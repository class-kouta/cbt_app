<?php

namespace App\Http\Controllers;

use App\Application\DTO\SafePlaceData;
use App\Application\UseCase\SafePlace\CreateSafePlaceUseCase;
use App\Application\UseCase\SafePlace\UpdateSafePlaceUseCase;
use App\Domain\Repository\SafePlaceRepositoryInterface;
use App\Http\Requests\SafePlace\CreateSafePlaceRequest;
use App\Http\Requests\SafePlace\UpdateSafePlaceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SafePlaceController extends Controller
{
    public function show(SafePlaceRepositoryInterface $repository): JsonResponse
    {
        $safePlace = $repository->findFirstForMember((int) Auth::id());

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
            'id' => $safePlace->getId(),
            'safe_image' => $safePlace->getSafeImage(),
            'safe_something' => $safePlace->getSafeSomething(),
            'created_at' => $safePlace->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $safePlace->getUpdatedAt()->format(DATE_ATOM),
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

    public function update(UpdateSafePlaceRequest $request, int $id, UpdateSafePlaceUseCase $updateSafePlace): JsonResponse
    {
        $data = new SafePlaceData(
            safeImage: $request->input('safe_image'),
            safeSomething: $request->input('safe_something')
        );
        $entity = $updateSafePlace->handle($id, $data);

        return response()->json([
            'id' => $entity->getId(),
            'safe_image' => $entity->getSafeImage(),
            'safe_something' => $entity->getSafeSomething(),
            'created_at' => $entity->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $entity->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }
}

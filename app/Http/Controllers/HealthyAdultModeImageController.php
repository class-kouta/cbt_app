<?php

namespace App\Http\Controllers;

use App\Application\DTO\HealthyAdultModeImageData;
use App\Application\UseCase\HealthyAdultModeImage\CreateHealthyAdultModeImageUseCase;
use App\Application\UseCase\HealthyAdultModeImage\UpdateHealthyAdultModeImageUseCase;
use App\Domain\Repository\HealthyAdultModeImageRepositoryInterface;
use App\Http\Requests\HealthyAdultModeImage\CreateHealthyAdultModeImageRequest;
use App\Http\Requests\HealthyAdultModeImage\UpdateHealthyAdultModeImageRequest;
use Illuminate\Http\JsonResponse;

class HealthyAdultModeImageController extends Controller
{
    public function show(HealthyAdultModeImageRepositoryInterface $repository): JsonResponse
    {
        $entity = $repository->findFirst();

        if ($entity === null) {
            return response()->json([
                'id' => null,
                'content' => null,
                'created_at' => null,
                'updated_at' => null,
            ]);
        }

        return response()->json([
            'id' => $entity->getId(),
            'content' => $entity->getContent(),
            'created_at' => $entity->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $entity->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    public function store(CreateHealthyAdultModeImageRequest $request, CreateHealthyAdultModeImageUseCase $useCase): JsonResponse
    {
        $data = new HealthyAdultModeImageData(
            content: $request->input('content')
        );
        $entity = $useCase->handle($data);

        return response()->json([
            'id' => $entity->getId(),
            'content' => $entity->getContent(),
            'created_at' => $entity->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $entity->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    public function update(UpdateHealthyAdultModeImageRequest $request, int $id, UpdateHealthyAdultModeImageUseCase $useCase): JsonResponse
    {
        $data = new HealthyAdultModeImageData(
            content: $request->input('content')
        );
        $entity = $useCase->handle($id, $data);

        return response()->json([
            'id' => $entity->getId(),
            'content' => $entity->getContent(),
            'created_at' => $entity->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $entity->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }
}

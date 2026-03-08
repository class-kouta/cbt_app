<?php

namespace App\Http\Controllers;

use App\Application\DTO\HappySchemaActionPlanData;
use App\Application\UseCase\HappySchemaActionPlan\CreateHappySchemaActionPlanUseCase;
use App\Application\UseCase\HappySchemaActionPlan\ExportHappySchemaActionPlanCsvUseCase;
use App\Application\UseCase\HappySchemaActionPlan\UpdateHappySchemaActionPlanUseCase;
use App\Domain\Entity\HappySchemaActionPlan;
use App\Domain\Repository\HappySchemaActionPlanRepositoryInterface;
use App\Http\Requests\HappySchemaActionPlan\CreateHappySchemaActionPlanRequest;
use App\Http\Requests\HappySchemaActionPlan\UpdateHappySchemaActionPlanRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HappySchemaActionPlanController extends Controller
{
    public function show(HappySchemaActionPlanRepositoryInterface $repository): JsonResponse
    {
        $plan = $repository->findFirst();

        if ($plan === null) {
            return response()->json([
                'id' => null,
                'happy_schema' => null,
                'action_plan' => null,
                'created_at' => null,
                'updated_at' => null,
            ]);
        }

        return $this->toResponse($plan);
    }

    public function store(CreateHappySchemaActionPlanRequest $request, CreateHappySchemaActionPlanUseCase $createPlan): JsonResponse
    {
        $data = new HappySchemaActionPlanData(
            happySchema: $request->input('happy_schema'),
            actionPlan: $request->input('action_plan')
        );

        return $this->toResponse($createPlan->handle($data), 201);
    }

    public function update(UpdateHappySchemaActionPlanRequest $request, int $id, UpdateHappySchemaActionPlanUseCase $updatePlan): JsonResponse
    {
        $data = new HappySchemaActionPlanData(
            happySchema: $request->input('happy_schema'),
            actionPlan: $request->input('action_plan')
        );

        return $this->toResponse($updatePlan->handle($id, $data));
    }

    public function exportCsv(ExportHappySchemaActionPlanCsvUseCase $exportUseCase): StreamedResponse
    {
        return $exportUseCase->handle();
    }

    private function toResponse(HappySchemaActionPlan $plan, int $status = 200): JsonResponse
    {
        return response()->json([
            'id' => $plan->getId(),
            'happy_schema' => $plan->getHappySchema(),
            'action_plan' => $plan->getActionPlan(),
            'created_at' => $plan->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $plan->getUpdatedAt()->format(DATE_ATOM),
        ], $status);
    }
}

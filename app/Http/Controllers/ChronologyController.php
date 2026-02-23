<?php

namespace App\Http\Controllers;

use App\Application\DTO\ChronologyData;
use App\Application\UseCase\Chronology\CreateChronologyUseCase;
use App\Application\UseCase\Chronology\UpdateChronologyUseCase;
use App\Application\UseCase\Chronology\DeleteChronologyUseCase;
use App\Http\Requests\Chronology\CreateChronologyRequest;
use App\Http\Requests\Chronology\UpdateChronologyRequest;
use App\Infrastructure\Database\Models\Chronology;
use Illuminate\Http\JsonResponse;

class ChronologyController extends Controller
{
    public function index(): JsonResponse
    {
        $chronologies = Chronology::orderByDesc('created_at')
            ->get()
            ->map(function ($chronology) {
                return [
                    'id' => $chronology->id,
                    'when_period' => $chronology->when_period,
                    'environment_event' => $chronology->environment_event,
                    'experience_feeling' => $chronology->experience_feeling,
                    'created_at' => $chronology->created_at->format(DATE_ATOM),
                    'updated_at' => $chronology->updated_at->format(DATE_ATOM),
                ];
            });

        return response()->json($chronologies);
    }

    public function show(Chronology $chronology): JsonResponse
    {
        return response()->json([
            'id' => $chronology->id,
            'when_period' => $chronology->when_period,
            'environment_event' => $chronology->environment_event,
            'experience_feeling' => $chronology->experience_feeling,
            'created_at' => $chronology->created_at->format(DATE_ATOM),
            'updated_at' => $chronology->updated_at->format(DATE_ATOM),
        ]);
    }

    public function store(
        CreateChronologyRequest $request,
        CreateChronologyUseCase $createChronology
    ): JsonResponse {
        $data = new ChronologyData(
            whenPeriod: (string) $request->string('when_period'),
            environmentEvent: $request->filled('environment_event') ? (string) $request->string('environment_event') : null,
            experienceFeeling: $request->filled('experience_feeling') ? (string) $request->string('experience_feeling') : null,
        );

        $chronology = $createChronology->handle($data);

        return response()->json([
            'id' => $chronology->getId(),
            'when_period' => $chronology->getWhenPeriod(),
            'environment_event' => $chronology->getEnvironmentEvent(),
            'experience_feeling' => $chronology->getExperienceFeeling(),
            'created_at' => $chronology->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $chronology->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    public function update(
        UpdateChronologyRequest $request,
        Chronology $chronology,
        UpdateChronologyUseCase $updateChronology
    ): JsonResponse {
        $data = new ChronologyData(
            whenPeriod: (string) $request->string('when_period'),
            environmentEvent: $request->filled('environment_event') ? (string) $request->string('environment_event') : null,
            experienceFeeling: $request->filled('experience_feeling') ? (string) $request->string('experience_feeling') : null,
        );

        $updatedChronology = $updateChronology->handle($chronology->id, $data);

        return response()->json([
            'id' => $updatedChronology->getId(),
            'when_period' => $updatedChronology->getWhenPeriod(),
            'environment_event' => $updatedChronology->getEnvironmentEvent(),
            'experience_feeling' => $updatedChronology->getExperienceFeeling(),
            'created_at' => $updatedChronology->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedChronology->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    public function destroy(
        Chronology $chronology,
        DeleteChronologyUseCase $deleteChronology
    ): JsonResponse {
        $deleteChronology->handle($chronology->id);

        return response()->json(null, 204);
    }
}

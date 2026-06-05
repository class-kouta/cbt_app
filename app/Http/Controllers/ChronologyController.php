<?php

namespace App\Http\Controllers;

use App\Application\DTO\ChronologyData;
use App\Application\UseCase\Chronology\CreateChronologyUseCase;
use App\Application\UseCase\Chronology\DeleteChronologyUseCase;
use App\Application\UseCase\Chronology\ExportChronologyCsvUseCase;
use App\Application\UseCase\Chronology\UpdateChronologyUseCase;
use App\Domain\Repository\ChronologyRepositoryInterface;
use App\Http\Requests\Chronology\CreateChronologyRequest;
use App\Http\Requests\Chronology\UpdateChronologyRequest;
use App\Infrastructure\Database\Models\Chronology;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChronologyController extends Controller
{
    public function index(): JsonResponse
    {
        $chronologies = array_map(function ($chronology) {
                return [
                    'id' => $chronology->getId(),
                    'when_period' => $chronology->getWhenPeriod(),
                    'environment_event' => $chronology->getEnvironmentEvent(),
                    'experience_feeling' => $chronology->getExperienceFeeling(),
                    'sentiment_type' => $chronology->getSentimentType(),
                    'created_at' => $chronology->getCreatedAt()->format(DATE_ATOM),
                    'updated_at' => $chronology->getUpdatedAt()->format(DATE_ATOM),
                ];
            }, app(ChronologyRepositoryInterface::class)->findAllForMember((int) Auth::id()));

        return response()->json($chronologies);
    }

    public function show(Chronology $chronology): JsonResponse
    {
        $this->authorizeMemberOwnership($chronology);

        return response()->json([
            'id' => $chronology->id,
            'when_period' => $chronology->when_period,
            'environment_event' => $chronology->environment_event,
            'experience_feeling' => $chronology->experience_feeling,
            'sentiment_type' => $chronology->sentiment_type,
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
            sentimentType: $request->filled('sentiment_type') ? (string) $request->string('sentiment_type') : null,
        );

        $chronology = $createChronology->handle($data);

        return response()->json([
            'id' => $chronology->getId(),
            'when_period' => $chronology->getWhenPeriod(),
            'environment_event' => $chronology->getEnvironmentEvent(),
            'experience_feeling' => $chronology->getExperienceFeeling(),
            'sentiment_type' => $chronology->getSentimentType(),
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
            sentimentType: $request->filled('sentiment_type') ? (string) $request->string('sentiment_type') : null,
        );

        $updatedChronology = $updateChronology->handle($chronology->id, $data);

        return response()->json([
            'id' => $updatedChronology->getId(),
            'when_period' => $updatedChronology->getWhenPeriod(),
            'environment_event' => $updatedChronology->getEnvironmentEvent(),
            'experience_feeling' => $updatedChronology->getExperienceFeeling(),
            'sentiment_type' => $updatedChronology->getSentimentType(),
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

    public function exportCsv(ExportChronologyCsvUseCase $exportUseCase): StreamedResponse
    {
        return $exportUseCase->handle();
    }
}

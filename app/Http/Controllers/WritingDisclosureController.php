<?php

namespace App\Http\Controllers;

use App\Application\DTO\WritingDisclosureData;
use App\Application\UseCase\WritingDisclosure\CreateWritingDisclosureUseCase;
use App\Application\UseCase\WritingDisclosure\UpdateWritingDisclosureUseCase;
use App\Application\UseCase\WritingDisclosure\DeleteWritingDisclosureUseCase;
use App\Http\Requests\WritingDisclosure\CreateWritingDisclosureRequest;
use App\Http\Requests\WritingDisclosure\UpdateWritingDisclosureRequest;
use App\Infrastructure\Database\Models\WritingDisclosure;
use Illuminate\Http\JsonResponse;

class WritingDisclosureController extends Controller
{
    /**
     * 筆記開示一覧を取得（作成日時降順）
     */
    public function index(): JsonResponse
    {
        $writingDisclosures = WritingDisclosure::orderByDesc('created_at')
            ->get()
            ->map(function ($writingDisclosure) {
                return [
                    'id' => $writingDisclosure->id,
                    'content' => $writingDisclosure->content,
                    'created_at' => $writingDisclosure->created_at->format(DATE_ATOM),
                    'updated_at' => $writingDisclosure->updated_at->format(DATE_ATOM),
                ];
            });

        return response()->json($writingDisclosures);
    }

    /**
     * 筆記開示を作成
     */
    public function store(
        CreateWritingDisclosureRequest $request,
        CreateWritingDisclosureUseCase $createWritingDisclosure
    ): JsonResponse {
        $data = new WritingDisclosureData(
            content: (string) $request->string('content')
        );

        $writingDisclosure = $createWritingDisclosure->handle($data);

        return response()->json([
            'id' => $writingDisclosure->getId(),
            'content' => $writingDisclosure->getContent(),
            'created_at' => $writingDisclosure->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $writingDisclosure->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * 筆記開示を更新
     */
    public function update(
        UpdateWritingDisclosureRequest $request,
        WritingDisclosure $writingDisclosure,
        UpdateWritingDisclosureUseCase $updateWritingDisclosure
    ): JsonResponse {
        $data = new WritingDisclosureData(
            content: (string) $request->string('content')
        );

        $updatedWritingDisclosure = $updateWritingDisclosure->handle($writingDisclosure->id, $data);

        return response()->json([
            'id' => $updatedWritingDisclosure->getId(),
            'content' => $updatedWritingDisclosure->getContent(),
            'created_at' => $updatedWritingDisclosure->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedWritingDisclosure->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * 筆記開示を削除
     */
    public function destroy(
        WritingDisclosure $writingDisclosure,
        DeleteWritingDisclosureUseCase $deleteWritingDisclosure
    ): JsonResponse {
        $deleteWritingDisclosure->handle($writingDisclosure->id);

        return response()->json(null, 204);
    }
}

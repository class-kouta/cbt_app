<?php

namespace App\Http\Controllers;

use App\Application\DTO\SchemaModeMonitoringData;
use App\Application\UseCase\SchemaModeMonitoring\CreateSchemaModeMonitoringUseCase;
use App\Application\UseCase\SchemaModeMonitoring\ListSchemaModeMonitoringsUseCase;
use App\Application\UseCase\SchemaModeMonitoring\UpdateSchemaModeMonitoringUseCase;
use App\Application\UseCase\SchemaModeMonitoring\DeleteSchemaModeMonitoringUseCase;
use App\Http\Requests\SchemaModeMonitoring\CreateSchemaModeMonitoringRequest;
use App\Http\Requests\SchemaModeMonitoring\UpdateSchemaModeMonitoringRequest;
use App\Infrastructure\Database\Models\SchemaModeMonitoring;
use Illuminate\Http\JsonResponse;

class SchemaModeMonitoringController extends Controller
{
    /**
     * セルフモニタリング一覧を取得（作成日時降順）
     */
    public function index(ListSchemaModeMonitoringsUseCase $listUseCase): JsonResponse
    {
        $items = $listUseCase->handle();

        return response()->json($items);
    }

    /**
     * セルフモニタリング詳細を取得
     */
    public function show(SchemaModeMonitoring $schemaModeMonitoring): JsonResponse
    {
        return response()->json([
            'id' => $schemaModeMonitoring->id,
            'content' => $schemaModeMonitoring->content,
            'created_at' => $schemaModeMonitoring->created_at->format(DATE_ATOM),
            'updated_at' => $schemaModeMonitoring->updated_at->format(DATE_ATOM),
        ]);
    }

    /**
     * セルフモニタリングを作成
     */
    public function store(
        CreateSchemaModeMonitoringRequest $request,
        CreateSchemaModeMonitoringUseCase $createSchemaModeMonitoring
    ): JsonResponse {
        $data = new SchemaModeMonitoringData(
            content: (string) $request->string('content')
        );

        $schemaModeMonitoring = $createSchemaModeMonitoring->handle($data);

        return response()->json([
            'id' => $schemaModeMonitoring->getId(),
            'content' => $schemaModeMonitoring->getContent(),
            'created_at' => $schemaModeMonitoring->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $schemaModeMonitoring->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * セルフモニタリングを更新
     */
    public function update(
        UpdateSchemaModeMonitoringRequest $request,
        SchemaModeMonitoring $schemaModeMonitoring,
        UpdateSchemaModeMonitoringUseCase $updateSchemaModeMonitoring
    ): JsonResponse {
        $data = new SchemaModeMonitoringData(
            content: (string) $request->string('content')
        );

        $updatedSchemaModeMonitoring = $updateSchemaModeMonitoring->handle($schemaModeMonitoring->id, $data);

        return response()->json([
            'id' => $updatedSchemaModeMonitoring->getId(),
            'content' => $updatedSchemaModeMonitoring->getContent(),
            'created_at' => $updatedSchemaModeMonitoring->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedSchemaModeMonitoring->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * セルフモニタリングを削除
     */
    public function destroy(
        SchemaModeMonitoring $schemaModeMonitoring,
        DeleteSchemaModeMonitoringUseCase $deleteSchemaModeMonitoring
    ): JsonResponse {
        $deleteSchemaModeMonitoring->handle($schemaModeMonitoring->id);

        return response()->json(null, 204);
    }
}

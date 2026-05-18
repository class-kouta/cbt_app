<?php

namespace App\Http\Controllers;

use App\Application\DTO\SupportNetworkData;
use App\Application\UseCase\SupportNetwork\CreateSupportNetworkUseCase;
use App\Application\UseCase\SupportNetwork\UpdateSupportNetworkUseCase;
use App\Application\UseCase\SupportNetwork\DeleteSupportNetworkUseCase;
use App\Application\UseCase\SupportNetwork\ListSupportNetworksUseCase;
use App\Http\Requests\SupportNetwork\CreateSupportNetworkRequest;
use App\Http\Requests\SupportNetwork\UpdateSupportNetworkRequest;
use App\Infrastructure\Database\Models\SupportNetwork;
use Illuminate\Http\JsonResponse;

class SupportNetworkController extends Controller
{
    /**
     * サポートネットワーク一覧を取得（ポイント高い順、同ポイントは作成日時降順）
     */
    public function index(ListSupportNetworksUseCase $listSupportNetworks): JsonResponse
    {
        $supportNetworks = collect($listSupportNetworks->handle())
            ->map(function ($supportNetwork) {
                return [
                    'id' => $supportNetwork->getId(),
                    'name' => $supportNetwork->getName(),
                    'point' => $supportNetwork->getPoint(),
                    'created_at' => $supportNetwork->getCreatedAt()->format(DATE_ATOM),
                    'updated_at' => $supportNetwork->getUpdatedAt()->format(DATE_ATOM),
                ];
            });

        return response()->json($supportNetworks);
    }

    /**
     * サポートネットワークを作成
     */
    public function store(CreateSupportNetworkRequest $request, CreateSupportNetworkUseCase $createSupportNetwork): JsonResponse
    {
        $data = new SupportNetworkData(
            name: (string) $request->string('name')
        );

        $supportNetwork = $createSupportNetwork->handle($data);

        return response()->json([
            'id' => $supportNetwork->getId(),
            'name' => $supportNetwork->getName(),
            'point' => $supportNetwork->getPoint(),
            'created_at' => $supportNetwork->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $supportNetwork->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * サポートネットワークを更新
     */
    public function update(UpdateSupportNetworkRequest $request, SupportNetwork $supportNetwork, UpdateSupportNetworkUseCase $updateSupportNetwork): JsonResponse
    {
        $data = new SupportNetworkData(
            name: (string) $request->string('name'),
            point: $request->has('point') ? (int) $request->integer('point') : null
        );

        $updatedSupportNetwork = $updateSupportNetwork->handle($supportNetwork->id, $data);

        return response()->json([
            'id' => $updatedSupportNetwork->getId(),
            'name' => $updatedSupportNetwork->getName(),
            'point' => $updatedSupportNetwork->getPoint(),
            'created_at' => $updatedSupportNetwork->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $updatedSupportNetwork->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * サポートネットワークを削除
     */
    public function destroy(SupportNetwork $supportNetwork, DeleteSupportNetworkUseCase $deleteSupportNetwork): JsonResponse
    {
        $deleteSupportNetwork->handle($supportNetwork->id);

        return response()->json(null, 204);
    }
}

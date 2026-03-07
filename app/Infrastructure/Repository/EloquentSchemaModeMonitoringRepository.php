<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SchemaModeMonitoring as SchemaModeMonitoringEntity;
use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;
use App\Infrastructure\Database\Models\SchemaModeMonitoring as SchemaModeMonitoringModel;
use DateTimeImmutable;

class EloquentSchemaModeMonitoringRepository implements SchemaModeMonitoringRepositoryInterface
{
    public function save(SchemaModeMonitoringEntity $schemaModeMonitoring): SchemaModeMonitoringEntity
    {
        $model = SchemaModeMonitoringModel::updateOrCreate(
            ['id' => $schemaModeMonitoring->getId()],
            ['content' => $schemaModeMonitoring->getContent()]
        );

        return SchemaModeMonitoringEntity::reconstitute(
            id: (int) $model->getKey(),
            content: (string) $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?SchemaModeMonitoringEntity
    {
        $model = SchemaModeMonitoringModel::find($id);

        if ($model === null) {
            return null;
        }

        return SchemaModeMonitoringEntity::reconstitute(
            id: (int) $model->id,
            content: (string) $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findAllOrderByCreatedAtDesc(): array
    {
        return SchemaModeMonitoringModel::orderByDesc('created_at')
            ->get()
            ->map(function ($model) {
                return [
                    'id' => $model->id,
                    'content' => $model->content,
                    'created_at' => $model->created_at->format(DATE_ATOM),
                    'updated_at' => $model->updated_at->format(DATE_ATOM),
                ];
            })
            ->toArray();
    }

    public function delete(int $id): void
    {
        SchemaModeMonitoringModel::destroy($id);
    }
}

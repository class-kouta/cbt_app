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
        if ($schemaModeMonitoring->getId() !== null) {
            $model = SchemaModeMonitoringModel::findOrFail($schemaModeMonitoring->getId());
            $model->content = $schemaModeMonitoring->getContent();
            $model->save();
        } else {
            $model = new SchemaModeMonitoringModel();
            $model->content = $schemaModeMonitoring->getContent();
            $model->save();
        }

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

    public function delete(int $id): void
    {
        $model = SchemaModeMonitoringModel::find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}

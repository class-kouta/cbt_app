<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SchemaModeMonitoring as SchemaModeMonitoringEntity;
use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;
use App\Infrastructure\Database\Models\SchemaModeMonitoring as SchemaModeMonitoringModel;
use Carbon\Carbon;
use DateTimeImmutable;

class EloquentSchemaModeMonitoringRepository implements SchemaModeMonitoringRepositoryInterface
{
    private function toDateTimeImmutable(mixed $value): DateTimeImmutable
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof Carbon) {
            return DateTimeImmutable::createFromMutable($value);
        }

        return new DateTimeImmutable((string) $value);
    }

    public function saveForMember(SchemaModeMonitoringEntity $schemaModeMonitoring, int $memberId): SchemaModeMonitoringEntity
    {
        if ($schemaModeMonitoring->getId() !== null) {
            $model = SchemaModeMonitoringModel::query()
                ->where('member_id', $memberId)
                ->findOrFail($schemaModeMonitoring->getId());
            $model->content = $schemaModeMonitoring->getContent();
            $model->save();
        } else {
            $model = new SchemaModeMonitoringModel();
            $model->member_id = $memberId;
            $model->content = $schemaModeMonitoring->getContent();
            $model->save();
        }

        return SchemaModeMonitoringEntity::reconstitute(
            id: (int) $model->getKey(),
            content: (string) $model->content,
            createdAt: $this->toDateTimeImmutable($model->created_at),
            updatedAt: $this->toDateTimeImmutable($model->updated_at),
        );
    }

    public function findByIdForMember(int $id, int $memberId): ?SchemaModeMonitoringEntity
    {
        $model = SchemaModeMonitoringModel::query()
            ->where('member_id', $memberId)
            ->find($id);

        if ($model === null) {
            return null;
        }

        return SchemaModeMonitoringEntity::reconstitute(
            id: (int) $model->id,
            content: (string) $model->content,
            createdAt: $this->toDateTimeImmutable($model->created_at),
            updatedAt: $this->toDateTimeImmutable($model->updated_at),
        );
    }

    public function findAllForMemberOrderByCreatedAtDesc(int $memberId): array
    {
        return SchemaModeMonitoringModel::query()
            ->where('member_id', $memberId)
            ->orderByDesc('created_at')
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

    public function deleteForMember(int $id, int $memberId): void
    {
        SchemaModeMonitoringModel::query()
            ->where('member_id', $memberId)
            ->whereKey($id)
            ->delete();
    }
}

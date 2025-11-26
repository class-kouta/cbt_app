<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Column as ColumnEntity;
use App\Domain\Repository\ColumnRepositoryInterface;
use App\Infrastructure\Database\Models\Column as ColumnModel;
use DateTimeImmutable;

class EloquentColumnRepository implements ColumnRepositoryInterface
{
    public function save(ColumnEntity $column): ColumnEntity
    {
        if ($column->getId() !== null) {
            // 更新
            $model = ColumnModel::findOrFail($column->getId());
            $model->situation = $column->getSituation();
            $model->mood = $column->getMood();
            $model->automatic_thought = $column->getAutomaticThought();
            $model->evidence = $column->getEvidence();
            $model->counter_evidence = $column->getCounterEvidence();
            $model->adaptive_thought = $column->getAdaptiveThought();
            $model->current_mood = $column->getCurrentMood();
            $model->save();
        } else {
            // 新規作成
            $model = new ColumnModel();
            $model->situation = $column->getSituation();
            $model->mood = $column->getMood();
            $model->automatic_thought = $column->getAutomaticThought();
            $model->evidence = $column->getEvidence();
            $model->counter_evidence = $column->getCounterEvidence();
            $model->adaptive_thought = $column->getAdaptiveThought();
            $model->current_mood = $column->getCurrentMood();
            $model->save();
        }

        return ColumnEntity::reconstitute(
            id: (int) $model->getKey(),
            situation: (string) $model->situation,
            mood: (string) $model->mood,
            automaticThought: (string) $model->automatic_thought,
            evidence: (string) $model->evidence,
            counterEvidence: (string) $model->counter_evidence,
            adaptiveThought: (string) $model->adaptive_thought,
            currentMood: (string) $model->current_mood,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?ColumnEntity
    {
        $model = ColumnModel::find($id);

        if ($model === null) {
            return null;
        }

        return ColumnEntity::reconstitute(
            id: (int) $model->id,
            situation: (string) $model->situation,
            mood: (string) $model->mood,
            automaticThought: (string) $model->automatic_thought,
            evidence: (string) $model->evidence,
            counterEvidence: (string) $model->counter_evidence,
            adaptiveThought: (string) $model->adaptive_thought,
            currentMood: (string) $model->current_mood,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function delete(int $id): void
    {
        $model = ColumnModel::find($id);

        if ($model !== null) {
            $model->delete();
        }
    }

    /**
     * @return ColumnEntity[]
     */
    public function findAll(): array
    {
        return ColumnModel::orderByDesc('created_at')
            ->get()
            ->map(function ($model) {
                return ColumnEntity::reconstitute(
                    id: (int) $model->id,
                    situation: (string) $model->situation,
                    mood: (string) $model->mood,
                    automaticThought: (string) $model->automatic_thought,
                    evidence: (string) $model->evidence,
                    counterEvidence: (string) $model->counter_evidence,
                    adaptiveThought: (string) $model->adaptive_thought,
                    currentMood: (string) $model->current_mood,
                    createdAt: new DateTimeImmutable($model->created_at),
                    updatedAt: new DateTimeImmutable($model->updated_at),
                );
            })
            ->toArray();
    }
}

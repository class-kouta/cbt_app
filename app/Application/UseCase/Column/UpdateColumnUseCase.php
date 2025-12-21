<?php

namespace App\Application\UseCase\Column;

use App\Application\DTO\ColumnData;
use App\Domain\Entity\Column as ColumnEntity;
use App\Domain\Repository\ColumnRepositoryInterface;

class UpdateColumnUseCase
{
    public function __construct(private readonly ColumnRepositoryInterface $columnRepository)
    {
    }

    public function handle(int $id, ColumnData $data): ColumnEntity
    {
        // 既存のコラムを取得
        $existingColumn = $this->columnRepository->findById($id);
        
        if ($existingColumn === null) {
            throw new \RuntimeException('Column not found');
        }

        // 新しいデータでエンティティを再構成
        $updatedColumn = ColumnEntity::reconstitute(
            id: $id,
            situation: $data->situation,
            mood: $data->mood,
            automaticThought: $data->automaticThought,
            evidence: $data->evidence,
            counterEvidence: $data->counterEvidence,
            adaptiveThought: $data->adaptiveThought,
            currentMood: $data->currentMood,
            notes: $data->notes,
            createdAt: $existingColumn->getCreatedAt(),
            updatedAt: new \DateTimeImmutable('now')
        );

        return $this->columnRepository->save($updatedColumn);
    }
}

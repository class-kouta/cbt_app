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

    /**
     * コラムを更新し、タグを同期する
     *
     * @param int $id コラムID
     * @param ColumnData $data コラムデータ
     * @return array<string, mixed> 更新結果（タグ情報を含む）
     */
    public function handle(int $id, ColumnData $data): array
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
            stressorAndResponseId: $data->stressorAndResponseId ?? $existingColumn->getStressorAndResponseId(),
            createdAt: $existingColumn->getCreatedAt(),
            updatedAt: new \DateTimeImmutable('now')
        );

        return $this->columnRepository->saveWithTags($updatedColumn, $data->tagIds);
    }
}

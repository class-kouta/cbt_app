<?php

namespace App\Application\UseCase\Column;

use App\Application\DTO\ColumnData;
use App\Domain\Entity\Column as ColumnEntity;
use App\Domain\Repository\ColumnRepositoryInterface;

class CreateColumnUseCase
{
    public function __construct(private readonly ColumnRepositoryInterface $columnRepository)
    {
    }

    /**
     * コラムを作成し、タグを同期する
     *
     * @param ColumnData $data コラムデータ
     * @return array<string, mixed> 作成結果（タグ情報を含む）
     */
    public function handle(ColumnData $data): array
    {
        $column = ColumnEntity::createNew(
            $data->situation,
            $data->mood,
            $data->automaticThought,
            $data->evidence,
            $data->counterEvidence,
            $data->adaptiveThought,
            $data->currentMood,
            $data->notes,
            $data->stressorAndResponseId
        );

        return $this->columnRepository->saveWithTags($column, $data->tagIds);
    }
}

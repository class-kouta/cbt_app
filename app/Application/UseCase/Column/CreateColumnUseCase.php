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

    public function handle(ColumnData $data): ColumnEntity
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

        return $this->columnRepository->save($column);
    }
}

<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Application\DTO\SimpleNotepadData;
use App\Domain\Entity\SimpleNotepad as SimpleNotepadEntity;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;

class CreateSimpleNotepadUseCase
{
    public function __construct(private readonly SimpleNotepadRepositoryInterface $simpleNotepadRepository)
    {
    }

    public function handle(SimpleNotepadData $data): SimpleNotepadEntity
    {
        $simpleNotepad = SimpleNotepadEntity::createNew($data->title, $data->content);
        return $this->simpleNotepadRepository->save($simpleNotepad);
    }
}

<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Application\DTO\SimpleNotepadData;
use App\Domain\Entity\SimpleNotepad as SimpleNotepadEntity;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use DomainException;

class UpdateSimpleNotepadUseCase
{
    public function __construct(private readonly SimpleNotepadRepositoryInterface $simpleNotepadRepository)
    {
    }

    public function handle(int $id, SimpleNotepadData $data): SimpleNotepadEntity
    {
        $simpleNotepad = $this->simpleNotepadRepository->findById($id);

        if ($simpleNotepad === null) {
            throw new DomainException('Simple notepad not found.');
        }

        $updatedSimpleNotepad = $simpleNotepad->update($data->content);
        return $this->simpleNotepadRepository->save($updatedSimpleNotepad);
    }
}

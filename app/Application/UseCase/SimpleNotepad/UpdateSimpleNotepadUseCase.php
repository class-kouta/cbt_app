<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Application\DTO\SimpleNotepadData;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use DomainException;

class UpdateSimpleNotepadUseCase
{
    public function __construct(private readonly SimpleNotepadRepositoryInterface $simpleNotepadRepository)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(int $id, SimpleNotepadData $data, int $memberId): array
    {
        $simpleNotepad = $this->simpleNotepadRepository->findByIdForMember($id, $memberId);

        if ($simpleNotepad === null) {
            throw new DomainException('Simple notepad not found.');
        }

        $updatedSimpleNotepad = $simpleNotepad->update($data->title, $data->content);

        return $this->simpleNotepadRepository->saveWithTagsForMember($updatedSimpleNotepad, $data->tagIds, $memberId);
    }
}

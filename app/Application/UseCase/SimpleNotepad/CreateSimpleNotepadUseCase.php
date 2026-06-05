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

    /**
     * @return array<string, mixed>
     */
    public function handle(SimpleNotepadData $data, int $memberId): array
    {
        $simpleNotepad = SimpleNotepadEntity::createNew($data->title, $data->content);

        return $this->simpleNotepadRepository->saveWithTagsForMember($simpleNotepad, $data->tagIds, $memberId);
    }
}

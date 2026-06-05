<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Domain\Repository\SimpleNotepadRepositoryInterface;

class ListSimpleNotepadsUseCase
{
    public function __construct(private readonly SimpleNotepadRepositoryInterface $simpleNotepadRepository)
    {
    }

    public function handle(int $memberId): array
    {
        return $this->simpleNotepadRepository->findAllWithTagsForMember($memberId);
    }
}

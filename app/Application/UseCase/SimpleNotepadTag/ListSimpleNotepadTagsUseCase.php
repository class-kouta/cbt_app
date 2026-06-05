<?php

namespace App\Application\UseCase\SimpleNotepadTag;

use App\Domain\Repository\SimpleNotepadTagRepositoryInterface;

class ListSimpleNotepadTagsUseCase
{
    public function __construct(private readonly SimpleNotepadTagRepositoryInterface $simpleNotepadTagRepository)
    {
    }

    public function handle(int $memberId): array
    {
        return $this->simpleNotepadTagRepository->findAllSummariesForMember($memberId);
    }
}

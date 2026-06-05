<?php

namespace App\Application\UseCase\SimpleNotepadTag;

use App\Domain\Repository\SimpleNotepadTagRepositoryInterface;
use DomainException;

class DeleteSimpleNotepadTagUseCase
{
    public function __construct(private readonly SimpleNotepadTagRepositoryInterface $simpleNotepadTagRepository)
    {
    }

    public function handle(int $id, int $memberId): void
    {
        $tag = $this->simpleNotepadTagRepository->findByIdForMember($id, $memberId);

        if ($tag === null) {
            throw new DomainException('タグが見つかりません');
        }

        $this->simpleNotepadTagRepository->deleteForMember($id, $memberId);
    }
}

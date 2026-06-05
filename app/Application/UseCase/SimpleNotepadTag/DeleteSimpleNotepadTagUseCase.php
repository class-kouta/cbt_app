<?php

namespace App\Application\UseCase\SimpleNotepadTag;

use App\Domain\Repository\SimpleNotepadTagRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class DeleteSimpleNotepadTagUseCase
{
    public function __construct(private readonly SimpleNotepadTagRepositoryInterface $simpleNotepadTagRepository)
    {
    }

    public function handle(int $id): void
    {
        $memberId = (int) Auth::id();
        $tag = $this->simpleNotepadTagRepository->findByIdForMember($id, $memberId);

        if ($tag === null) {
            throw new DomainException('タグが見つかりません');
        }

        $this->simpleNotepadTagRepository->deleteForMember($id, $memberId);
    }
}

<?php

namespace App\Application\UseCase\SimpleNotepadTag;

use App\Domain\Repository\SimpleNotepadTagRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ListSimpleNotepadTagsUseCase
{
    public function __construct(private readonly SimpleNotepadTagRepositoryInterface $simpleNotepadTagRepository)
    {
    }

    public function handle(): array
    {
        $memberId = (int) Auth::id();

        return $this->simpleNotepadTagRepository->findAllForMember($memberId);
    }
}

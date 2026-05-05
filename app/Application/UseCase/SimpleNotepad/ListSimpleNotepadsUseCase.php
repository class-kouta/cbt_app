<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ListSimpleNotepadsUseCase
{
    public function __construct(private readonly SimpleNotepadRepositoryInterface $simpleNotepadRepository)
    {
    }

    public function handle(): array
    {
        $memberId = (int) Auth::id();
        return $this->simpleNotepadRepository->findAllForMember($memberId);
    }
}

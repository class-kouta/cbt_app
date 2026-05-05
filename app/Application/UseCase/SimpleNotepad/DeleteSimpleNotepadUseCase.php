<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteSimpleNotepadUseCase
{
    public function __construct(private readonly SimpleNotepadRepositoryInterface $simpleNotepadRepository)
    {
    }

    public function handle(int $id): void
    {
        $memberId = (int) Auth::id();
        $this->simpleNotepadRepository->deleteForMember($id, $memberId);
    }
}

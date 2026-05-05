<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Application\DTO\SimpleNotepadData;
use App\Domain\Entity\SimpleNotepad as SimpleNotepadEntity;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateSimpleNotepadUseCase
{
    public function __construct(private readonly SimpleNotepadRepositoryInterface $simpleNotepadRepository)
    {
    }

    public function handle(SimpleNotepadData $data): SimpleNotepadEntity
    {
        $memberId = (int) Auth::id();
        $simpleNotepad = SimpleNotepadEntity::createNew($data->title, $data->content);
        return $this->simpleNotepadRepository->saveForMember($simpleNotepad, $memberId);
    }
}

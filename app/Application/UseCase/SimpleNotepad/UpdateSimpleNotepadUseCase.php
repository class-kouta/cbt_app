<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Application\DTO\SimpleNotepadData;
use App\Domain\Entity\SimpleNotepad as SimpleNotepadEntity;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateSimpleNotepadUseCase
{
    public function __construct(private readonly SimpleNotepadRepositoryInterface $simpleNotepadRepository)
    {
    }

    public function handle(int $id, SimpleNotepadData $data): SimpleNotepadEntity
    {
        $memberId = (int) Auth::id();
        $simpleNotepad = $this->simpleNotepadRepository->findByIdForMember($id, $memberId);

        if ($simpleNotepad === null) {
            throw new DomainException('Simple notepad not found.');
        }

        $updatedSimpleNotepad = $simpleNotepad->update($data->title, $data->content);
        return $this->simpleNotepadRepository->saveForMember($updatedSimpleNotepad, $memberId);
    }
}

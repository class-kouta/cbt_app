<?php

namespace App\Application\UseCase\SimpleNotepadTag;

use App\Application\DTO\SimpleNotepadTagData;
use App\Domain\Entity\SimpleNotepadTag as SimpleNotepadTagEntity;
use App\Domain\Repository\SimpleNotepadTagRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateSimpleNotepadTagUseCase
{
    public function __construct(private readonly SimpleNotepadTagRepositoryInterface $simpleNotepadTagRepository)
    {
    }

    public function handle(int $id, SimpleNotepadTagData $data): SimpleNotepadTagEntity
    {
        $memberId = (int) Auth::id();
        $tag = $this->simpleNotepadTagRepository->findByIdForMember($id, $memberId);

        if ($tag === null) {
            throw new DomainException('タグが見つかりません');
        }

        $updatedTag = $tag->updateName($data->name);

        return $this->simpleNotepadTagRepository->saveForMember($updatedTag, $memberId);
    }
}

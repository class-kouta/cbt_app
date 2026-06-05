<?php

namespace App\Application\UseCase\SimpleNotepadTag;

use App\Application\DTO\SimpleNotepadTagData;
use App\Domain\Entity\SimpleNotepadTag as SimpleNotepadTagEntity;
use App\Domain\Repository\SimpleNotepadTagRepositoryInterface;
use DomainException;

class CreateSimpleNotepadTagUseCase
{
    private const MAX_TAGS = 10;

    public function __construct(private readonly SimpleNotepadTagRepositoryInterface $simpleNotepadTagRepository)
    {
    }

    public function handle(SimpleNotepadTagData $data, int $memberId): SimpleNotepadTagEntity
    {
        if ($this->simpleNotepadTagRepository->countForMember($memberId) >= self::MAX_TAGS) {
            throw new DomainException('タグは10個まで作成できます');
        }

        $tag = SimpleNotepadTagEntity::createNew($data->name);

        return $this->simpleNotepadTagRepository->saveForMember($tag, $memberId);
    }
}

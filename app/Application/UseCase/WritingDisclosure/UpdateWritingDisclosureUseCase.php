<?php

namespace App\Application\UseCase\WritingDisclosure;

use App\Application\DTO\WritingDisclosureData;
use App\Domain\Entity\WritingDisclosure as WritingDisclosureEntity;
use App\Domain\Repository\WritingDisclosureRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateWritingDisclosureUseCase
{
    public function __construct(private readonly WritingDisclosureRepositoryInterface $writingDisclosureRepository)
    {
    }

    public function handle(int $id, WritingDisclosureData $data): WritingDisclosureEntity
    {
        $memberId = (int) Auth::id();
        $writingDisclosure = $this->writingDisclosureRepository->findByIdForMember($id, $memberId);

        if ($writingDisclosure === null) {
            throw new DomainException('Writing disclosure not found.');
        }

        $updatedWritingDisclosure = $writingDisclosure->update($data->content);
        return $this->writingDisclosureRepository->saveForMember($updatedWritingDisclosure, $memberId);
    }
}

<?php

namespace App\Application\UseCase\WritingDisclosure;

use App\Application\DTO\WritingDisclosureData;
use App\Domain\Entity\WritingDisclosure as WritingDisclosureEntity;
use App\Domain\Repository\WritingDisclosureRepositoryInterface;
use DomainException;

class UpdateWritingDisclosureUseCase
{
    public function __construct(private readonly WritingDisclosureRepositoryInterface $writingDisclosureRepository)
    {
    }

    public function handle(int $id, WritingDisclosureData $data): WritingDisclosureEntity
    {
        $writingDisclosure = $this->writingDisclosureRepository->findById($id);

        if ($writingDisclosure === null) {
            throw new DomainException('Writing disclosure not found.');
        }

        $updatedWritingDisclosure = $writingDisclosure->update($data->content);
        return $this->writingDisclosureRepository->save($updatedWritingDisclosure);
    }
}

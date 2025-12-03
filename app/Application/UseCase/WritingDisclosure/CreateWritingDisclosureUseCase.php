<?php

namespace App\Application\UseCase\WritingDisclosure;

use App\Application\DTO\WritingDisclosureData;
use App\Domain\Entity\WritingDisclosure as WritingDisclosureEntity;
use App\Domain\Repository\WritingDisclosureRepositoryInterface;

class CreateWritingDisclosureUseCase
{
    public function __construct(private readonly WritingDisclosureRepositoryInterface $writingDisclosureRepository)
    {
    }

    public function handle(WritingDisclosureData $data): WritingDisclosureEntity
    {
        $writingDisclosure = WritingDisclosureEntity::createNew($data->content);
        return $this->writingDisclosureRepository->save($writingDisclosure);
    }
}

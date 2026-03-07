<?php

namespace App\Application\UseCase\Chronology;

use App\Application\DTO\ChronologyData;
use App\Domain\Entity\Chronology as ChronologyEntity;
use App\Domain\Repository\ChronologyRepositoryInterface;
use DomainException;

class UpdateChronologyUseCase
{
    public function __construct(private readonly ChronologyRepositoryInterface $chronologyRepository) {}

    public function handle(int $id, ChronologyData $data): ChronologyEntity
    {
        $chronology = $this->chronologyRepository->findById($id);

        if ($chronology === null) {
            throw new DomainException('Chronology not found.');
        }

        $updatedChronology = $chronology->update(
            $data->whenPeriod,
            $data->environmentEvent,
            $data->experienceFeeling,
            $data->sentimentType
        );

        return $this->chronologyRepository->save($updatedChronology);
    }
}

<?php

namespace App\Application\UseCase\EarlyMaladaptiveSchema;

use App\Application\DTO\EarlyMaladaptiveSchemaData;
use App\Domain\Entity\EarlyMaladaptiveSchema as EarlyMaladaptiveSchemaEntity;
use App\Domain\Repository\EarlyMaladaptiveSchemaRepositoryInterface;

class UpdateEarlyMaladaptiveSchemaUseCase
{
    public function __construct(private readonly EarlyMaladaptiveSchemaRepositoryInterface $repository)
    {
    }

    public function handle(int $id, EarlyMaladaptiveSchemaData $data): EarlyMaladaptiveSchemaEntity
    {
        $existingSchema = $this->repository->findById($id);

        if ($existingSchema === null) {
            throw new \RuntimeException('早期不適応スキーマが見つかりません');
        }

        $schema = EarlyMaladaptiveSchemaEntity::reconstitute(
            $id,
            $data->abandonment,
            $data->mistrustAbuse,
            $data->emotionalDeprivation,
            $data->defectivenessShame,
            $data->socialIsolation,
            $data->dependenceIncompetence,
            $data->vulnerabilityToHarm,
            $data->enmeshment,
            $data->failure,
            $data->entitlementGrandiosity,
            $data->insufficientSelfControl,
            $data->subjugation,
            $data->selfSacrifice,
            $data->approvalSeeking,
            $data->negativityPessimism,
            $data->emotionalInhibition,
            $data->unrelentingStandards,
            $data->punitiveness,
            $existingSchema->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->save($schema);
    }
}

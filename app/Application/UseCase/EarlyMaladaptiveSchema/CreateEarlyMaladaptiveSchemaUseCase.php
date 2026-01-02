<?php

namespace App\Application\UseCase\EarlyMaladaptiveSchema;

use App\Application\DTO\EarlyMaladaptiveSchemaData;
use App\Domain\Entity\EarlyMaladaptiveSchema as EarlyMaladaptiveSchemaEntity;
use App\Domain\Repository\EarlyMaladaptiveSchemaRepositoryInterface;

class CreateEarlyMaladaptiveSchemaUseCase
{
    public function __construct(private readonly EarlyMaladaptiveSchemaRepositoryInterface $repository)
    {
    }

    public function handle(EarlyMaladaptiveSchemaData $data): EarlyMaladaptiveSchemaEntity
    {
        $schema = EarlyMaladaptiveSchemaEntity::createNew(
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
            $data->punitiveness
        );

        return $this->repository->save($schema);
    }
}

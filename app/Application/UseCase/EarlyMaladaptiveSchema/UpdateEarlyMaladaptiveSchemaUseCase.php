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
            $data->abandonmentExperience,
            $data->mistrustAbuse,
            $data->mistrustAbuseExperience,
            $data->emotionalDeprivation,
            $data->emotionalDeprivationExperience,
            $data->defectivenessShame,
            $data->defectivenessShameExperience,
            $data->socialIsolation,
            $data->socialIsolationExperience,
            $data->dependenceIncompetence,
            $data->dependenceIncompetenceExperience,
            $data->vulnerabilityToHarm,
            $data->vulnerabilityToHarmExperience,
            $data->enmeshment,
            $data->enmeshmentExperience,
            $data->failure,
            $data->failureExperience,
            $data->entitlementGrandiosity,
            $data->entitlementGrandiosityExperience,
            $data->insufficientSelfControl,
            $data->insufficientSelfControlExperience,
            $data->subjugation,
            $data->subjugationExperience,
            $data->selfSacrifice,
            $data->selfSacrificeExperience,
            $data->approvalSeeking,
            $data->approvalSeekingExperience,
            $data->negativityPessimism,
            $data->negativityPessimismExperience,
            $data->emotionalInhibition,
            $data->emotionalInhibitionExperience,
            $data->unrelentingStandards,
            $data->unrelentingStandardsExperience,
            $data->punitiveness,
            $data->punitivenessExperience,
            $data->notes,
            $existingSchema->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->save($schema);
    }
}

<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\EarlyMaladaptiveSchema as EarlyMaladaptiveSchemaEntity;
use App\Domain\Repository\EarlyMaladaptiveSchemaRepositoryInterface;
use App\Infrastructure\Database\Models\EarlyMaladaptiveSchema as EarlyMaladaptiveSchemaModel;
use DateTimeImmutable;

class EloquentEarlyMaladaptiveSchemaRepository implements EarlyMaladaptiveSchemaRepositoryInterface
{
    public function saveForMember(EarlyMaladaptiveSchemaEntity $schema, int $memberId): EarlyMaladaptiveSchemaEntity
    {
        if ($schema->getId() !== null) {
            $model = EarlyMaladaptiveSchemaModel::where('member_id', $memberId)->findOrFail($schema->getId());
        } else {
            $model = new EarlyMaladaptiveSchemaModel();
            $model->member_id = $memberId;
        }

        $model->abandonment = $schema->getAbandonment();
        $model->abandonment_experience = $schema->getAbandonmentExperience();
        $model->mistrust_abuse = $schema->getMistrustAbuse();
        $model->mistrust_abuse_experience = $schema->getMistrustAbuseExperience();
        $model->emotional_deprivation = $schema->getEmotionalDeprivation();
        $model->emotional_deprivation_experience = $schema->getEmotionalDeprivationExperience();
        $model->defectiveness_shame = $schema->getDefectivenessShame();
        $model->defectiveness_shame_experience = $schema->getDefectivenessShameExperience();
        $model->social_isolation = $schema->getSocialIsolation();
        $model->social_isolation_experience = $schema->getSocialIsolationExperience();
        $model->dependence_incompetence = $schema->getDependenceIncompetence();
        $model->dependence_incompetence_experience = $schema->getDependenceIncompetenceExperience();
        $model->vulnerability_to_harm = $schema->getVulnerabilityToHarm();
        $model->vulnerability_to_harm_experience = $schema->getVulnerabilityToHarmExperience();
        $model->enmeshment = $schema->getEnmeshment();
        $model->enmeshment_experience = $schema->getEnmeshmentExperience();
        $model->failure = $schema->getFailure();
        $model->failure_experience = $schema->getFailureExperience();
        $model->entitlement_grandiosity = $schema->getEntitlementGrandiosity();
        $model->entitlement_grandiosity_experience = $schema->getEntitlementGrandiosityExperience();
        $model->insufficient_self_control = $schema->getInsufficientSelfControl();
        $model->insufficient_self_control_experience = $schema->getInsufficientSelfControlExperience();
        $model->subjugation = $schema->getSubjugation();
        $model->subjugation_experience = $schema->getSubjugationExperience();
        $model->self_sacrifice = $schema->getSelfSacrifice();
        $model->self_sacrifice_experience = $schema->getSelfSacrificeExperience();
        $model->approval_seeking = $schema->getApprovalSeeking();
        $model->approval_seeking_experience = $schema->getApprovalSeekingExperience();
        $model->negativity_pessimism = $schema->getNegativityPessimism();
        $model->negativity_pessimism_experience = $schema->getNegativityPessimismExperience();
        $model->emotional_inhibition = $schema->getEmotionalInhibition();
        $model->emotional_inhibition_experience = $schema->getEmotionalInhibitionExperience();
        $model->unrelenting_standards = $schema->getUnrelentingStandards();
        $model->unrelenting_standards_experience = $schema->getUnrelentingStandardsExperience();
        $model->punitiveness = $schema->getPunitiveness();
        $model->punitiveness_experience = $schema->getPunitivenessExperience();
        $model->notes = $schema->getNotes();
        $model->save();

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?EarlyMaladaptiveSchemaEntity
    {
        $model = EarlyMaladaptiveSchemaModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirstForMember(int $memberId): ?EarlyMaladaptiveSchemaEntity
    {
        $model = EarlyMaladaptiveSchemaModel::where('member_id', $memberId)->first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    private function toEntity(EarlyMaladaptiveSchemaModel $model): EarlyMaladaptiveSchemaEntity
    {
        return EarlyMaladaptiveSchemaEntity::reconstitute(
            id: (int) $model->getKey(),
            abandonment: $model->abandonment,
            abandonmentExperience: $model->abandonment_experience,
            mistrustAbuse: $model->mistrust_abuse,
            mistrustAbuseExperience: $model->mistrust_abuse_experience,
            emotionalDeprivation: $model->emotional_deprivation,
            emotionalDeprivationExperience: $model->emotional_deprivation_experience,
            defectivenessShame: $model->defectiveness_shame,
            defectivenessShameExperience: $model->defectiveness_shame_experience,
            socialIsolation: $model->social_isolation,
            socialIsolationExperience: $model->social_isolation_experience,
            dependenceIncompetence: $model->dependence_incompetence,
            dependenceIncompetenceExperience: $model->dependence_incompetence_experience,
            vulnerabilityToHarm: $model->vulnerability_to_harm,
            vulnerabilityToHarmExperience: $model->vulnerability_to_harm_experience,
            enmeshment: $model->enmeshment,
            enmeshmentExperience: $model->enmeshment_experience,
            failure: $model->failure,
            failureExperience: $model->failure_experience,
            entitlementGrandiosity: $model->entitlement_grandiosity,
            entitlementGrandiosityExperience: $model->entitlement_grandiosity_experience,
            insufficientSelfControl: $model->insufficient_self_control,
            insufficientSelfControlExperience: $model->insufficient_self_control_experience,
            subjugation: $model->subjugation,
            subjugationExperience: $model->subjugation_experience,
            selfSacrifice: $model->self_sacrifice,
            selfSacrificeExperience: $model->self_sacrifice_experience,
            approvalSeeking: $model->approval_seeking,
            approvalSeekingExperience: $model->approval_seeking_experience,
            negativityPessimism: $model->negativity_pessimism,
            negativityPessimismExperience: $model->negativity_pessimism_experience,
            emotionalInhibition: $model->emotional_inhibition,
            emotionalInhibitionExperience: $model->emotional_inhibition_experience,
            unrelentingStandards: $model->unrelenting_standards,
            unrelentingStandardsExperience: $model->unrelenting_standards_experience,
            punitiveness: $model->punitiveness,
            punitivenessExperience: $model->punitiveness_experience,
            notes: $model->notes,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }
}

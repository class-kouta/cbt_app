<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\EarlyMaladaptiveSchema as EarlyMaladaptiveSchemaEntity;
use App\Domain\Repository\EarlyMaladaptiveSchemaRepositoryInterface;
use App\Infrastructure\Database\Models\EarlyMaladaptiveSchema as EarlyMaladaptiveSchemaModel;
use DateTimeImmutable;

class EloquentEarlyMaladaptiveSchemaRepository implements EarlyMaladaptiveSchemaRepositoryInterface
{
    public function save(EarlyMaladaptiveSchemaEntity $schema): EarlyMaladaptiveSchemaEntity
    {
        if ($schema->getId() !== null) {
            // 更新
            $model = EarlyMaladaptiveSchemaModel::findOrFail($schema->getId());
        } else {
            // 新規作成
            $model = new EarlyMaladaptiveSchemaModel();
        }

        $model->abandonment = $schema->getAbandonment();
        $model->mistrust_abuse = $schema->getMistrustAbuse();
        $model->emotional_deprivation = $schema->getEmotionalDeprivation();
        $model->defectiveness_shame = $schema->getDefectivenessShame();
        $model->social_isolation = $schema->getSocialIsolation();
        $model->dependence_incompetence = $schema->getDependenceIncompetence();
        $model->vulnerability_to_harm = $schema->getVulnerabilityToHarm();
        $model->enmeshment = $schema->getEnmeshment();
        $model->failure = $schema->getFailure();
        $model->entitlement_grandiosity = $schema->getEntitlementGrandiosity();
        $model->insufficient_self_control = $schema->getInsufficientSelfControl();
        $model->subjugation = $schema->getSubjugation();
        $model->self_sacrifice = $schema->getSelfSacrifice();
        $model->approval_seeking = $schema->getApprovalSeeking();
        $model->negativity_pessimism = $schema->getNegativismPessimism();
        $model->emotional_inhibition = $schema->getEmotionalInhibition();
        $model->unrelenting_standards = $schema->getUnrelentingStandards();
        $model->punitiveness = $schema->getPunitiveness();
        $model->save();

        return $this->toEntity($model);
    }

    public function findById(int $id): ?EarlyMaladaptiveSchemaEntity
    {
        $model = EarlyMaladaptiveSchemaModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirst(): ?EarlyMaladaptiveSchemaEntity
    {
        $model = EarlyMaladaptiveSchemaModel::first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        $model = EarlyMaladaptiveSchemaModel::find($id);

        if ($model !== null) {
            $model->delete();
        }
    }

    private function toEntity(EarlyMaladaptiveSchemaModel $model): EarlyMaladaptiveSchemaEntity
    {
        return EarlyMaladaptiveSchemaEntity::reconstitute(
            id: (int) $model->getKey(),
            abandonment: $model->abandonment,
            mistrustAbuse: $model->mistrust_abuse,
            emotionalDeprivation: $model->emotional_deprivation,
            defectivenessShame: $model->defectiveness_shame,
            socialIsolation: $model->social_isolation,
            dependenceIncompetence: $model->dependence_incompetence,
            vulnerabilityToHarm: $model->vulnerability_to_harm,
            enmeshment: $model->enmeshment,
            failure: $model->failure,
            entitlementGrandiosity: $model->entitlement_grandiosity,
            insufficientSelfControl: $model->insufficient_self_control,
            subjugation: $model->subjugation,
            selfSacrifice: $model->self_sacrifice,
            approvalSeeking: $model->approval_seeking,
            negativityPessimism: $model->negativity_pessimism,
            emotionalInhibition: $model->emotional_inhibition,
            unrelentingStandards: $model->unrelenting_standards,
            punitiveness: $model->punitiveness,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }
}

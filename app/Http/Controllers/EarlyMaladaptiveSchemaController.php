<?php

namespace App\Http\Controllers;

use App\Application\DTO\EarlyMaladaptiveSchemaData;
use App\Application\UseCase\EarlyMaladaptiveSchema\CreateEarlyMaladaptiveSchemaUseCase;
use App\Application\UseCase\EarlyMaladaptiveSchema\UpdateEarlyMaladaptiveSchemaUseCase;
use App\Domain\Entity\EarlyMaladaptiveSchema as EarlyMaladaptiveSchemaEntity;
use App\Domain\Repository\EarlyMaladaptiveSchemaRepositoryInterface;
use App\Http\Requests\EarlyMaladaptiveSchema\CreateEarlyMaladaptiveSchemaRequest;
use App\Http\Requests\EarlyMaladaptiveSchema\UpdateEarlyMaladaptiveSchemaRequest;
use App\Infrastructure\Database\Models\EarlyMaladaptiveSchema;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EarlyMaladaptiveSchemaController extends Controller
{
    /**
     * 最初のスキーマを取得（存在しない場合は空のデータを返す）
     */
    public function show(EarlyMaladaptiveSchemaRepositoryInterface $repository): JsonResponse
    {
        $schema = $repository->findFirstForMember((int) Auth::id());

        if ($schema === null) {
            return response()->json([
                'id' => null,
                'abandonment' => null,
                'abandonment_experience' => null,
                'mistrust_abuse' => null,
                'mistrust_abuse_experience' => null,
                'emotional_deprivation' => null,
                'emotional_deprivation_experience' => null,
                'defectiveness_shame' => null,
                'defectiveness_shame_experience' => null,
                'social_isolation' => null,
                'social_isolation_experience' => null,
                'dependence_incompetence' => null,
                'dependence_incompetence_experience' => null,
                'vulnerability_to_harm' => null,
                'vulnerability_to_harm_experience' => null,
                'enmeshment' => null,
                'enmeshment_experience' => null,
                'failure' => null,
                'failure_experience' => null,
                'entitlement_grandiosity' => null,
                'entitlement_grandiosity_experience' => null,
                'insufficient_self_control' => null,
                'insufficient_self_control_experience' => null,
                'subjugation' => null,
                'subjugation_experience' => null,
                'self_sacrifice' => null,
                'self_sacrifice_experience' => null,
                'approval_seeking' => null,
                'approval_seeking_experience' => null,
                'negativity_pessimism' => null,
                'negativity_pessimism_experience' => null,
                'emotional_inhibition' => null,
                'emotional_inhibition_experience' => null,
                'unrelenting_standards' => null,
                'unrelenting_standards_experience' => null,
                'punitiveness' => null,
                'punitiveness_experience' => null,
                'notes' => null,
                'created_at' => null,
                'updated_at' => null,
            ]);
        }

        return response()->json($this->schemaToArray($schema));
    }

    /**
     * スキーマを作成
     */
    public function store(CreateEarlyMaladaptiveSchemaRequest $request, CreateEarlyMaladaptiveSchemaUseCase $createSchema): JsonResponse
    {
        $data = $this->buildDataFromRequest($request);
        $schema = $createSchema->handle($data);

        return response()->json([
            'id' => $schema->getId(),
            'abandonment' => $schema->getAbandonment(),
            'abandonment_experience' => $schema->getAbandonmentExperience(),
            'mistrust_abuse' => $schema->getMistrustAbuse(),
            'mistrust_abuse_experience' => $schema->getMistrustAbuseExperience(),
            'emotional_deprivation' => $schema->getEmotionalDeprivation(),
            'emotional_deprivation_experience' => $schema->getEmotionalDeprivationExperience(),
            'defectiveness_shame' => $schema->getDefectivenessShame(),
            'defectiveness_shame_experience' => $schema->getDefectivenessShameExperience(),
            'social_isolation' => $schema->getSocialIsolation(),
            'social_isolation_experience' => $schema->getSocialIsolationExperience(),
            'dependence_incompetence' => $schema->getDependenceIncompetence(),
            'dependence_incompetence_experience' => $schema->getDependenceIncompetenceExperience(),
            'vulnerability_to_harm' => $schema->getVulnerabilityToHarm(),
            'vulnerability_to_harm_experience' => $schema->getVulnerabilityToHarmExperience(),
            'enmeshment' => $schema->getEnmeshment(),
            'enmeshment_experience' => $schema->getEnmeshmentExperience(),
            'failure' => $schema->getFailure(),
            'failure_experience' => $schema->getFailureExperience(),
            'entitlement_grandiosity' => $schema->getEntitlementGrandiosity(),
            'entitlement_grandiosity_experience' => $schema->getEntitlementGrandiosityExperience(),
            'insufficient_self_control' => $schema->getInsufficientSelfControl(),
            'insufficient_self_control_experience' => $schema->getInsufficientSelfControlExperience(),
            'subjugation' => $schema->getSubjugation(),
            'subjugation_experience' => $schema->getSubjugationExperience(),
            'self_sacrifice' => $schema->getSelfSacrifice(),
            'self_sacrifice_experience' => $schema->getSelfSacrificeExperience(),
            'approval_seeking' => $schema->getApprovalSeeking(),
            'approval_seeking_experience' => $schema->getApprovalSeekingExperience(),
            'negativity_pessimism' => $schema->getNegativismPessimism(),
            'negativity_pessimism_experience' => $schema->getNegativismPessimismExperience(),
            'emotional_inhibition' => $schema->getEmotionalInhibition(),
            'emotional_inhibition_experience' => $schema->getEmotionalInhibitionExperience(),
            'unrelenting_standards' => $schema->getUnrelentingStandards(),
            'unrelenting_standards_experience' => $schema->getUnrelentingStandardsExperience(),
            'punitiveness' => $schema->getPunitiveness(),
            'punitiveness_experience' => $schema->getPunitivenessExperience(),
            'notes' => $schema->getNotes(),
            'created_at' => $schema->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $schema->getUpdatedAt()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * スキーマを更新
     */
    public function update(UpdateEarlyMaladaptiveSchemaRequest $request, EarlyMaladaptiveSchema $earlyMaladaptiveSchema, UpdateEarlyMaladaptiveSchemaUseCase $updateSchema): JsonResponse
    {
        $data = $this->buildDataFromRequest($request);
        $schema = $updateSchema->handle($earlyMaladaptiveSchema->id, $data);

        return response()->json([
            'id' => $schema->getId(),
            'abandonment' => $schema->getAbandonment(),
            'abandonment_experience' => $schema->getAbandonmentExperience(),
            'mistrust_abuse' => $schema->getMistrustAbuse(),
            'mistrust_abuse_experience' => $schema->getMistrustAbuseExperience(),
            'emotional_deprivation' => $schema->getEmotionalDeprivation(),
            'emotional_deprivation_experience' => $schema->getEmotionalDeprivationExperience(),
            'defectiveness_shame' => $schema->getDefectivenessShame(),
            'defectiveness_shame_experience' => $schema->getDefectivenessShameExperience(),
            'social_isolation' => $schema->getSocialIsolation(),
            'social_isolation_experience' => $schema->getSocialIsolationExperience(),
            'dependence_incompetence' => $schema->getDependenceIncompetence(),
            'dependence_incompetence_experience' => $schema->getDependenceIncompetenceExperience(),
            'vulnerability_to_harm' => $schema->getVulnerabilityToHarm(),
            'vulnerability_to_harm_experience' => $schema->getVulnerabilityToHarmExperience(),
            'enmeshment' => $schema->getEnmeshment(),
            'enmeshment_experience' => $schema->getEnmeshmentExperience(),
            'failure' => $schema->getFailure(),
            'failure_experience' => $schema->getFailureExperience(),
            'entitlement_grandiosity' => $schema->getEntitlementGrandiosity(),
            'entitlement_grandiosity_experience' => $schema->getEntitlementGrandiosityExperience(),
            'insufficient_self_control' => $schema->getInsufficientSelfControl(),
            'insufficient_self_control_experience' => $schema->getInsufficientSelfControlExperience(),
            'subjugation' => $schema->getSubjugation(),
            'subjugation_experience' => $schema->getSubjugationExperience(),
            'self_sacrifice' => $schema->getSelfSacrifice(),
            'self_sacrifice_experience' => $schema->getSelfSacrificeExperience(),
            'approval_seeking' => $schema->getApprovalSeeking(),
            'approval_seeking_experience' => $schema->getApprovalSeekingExperience(),
            'negativity_pessimism' => $schema->getNegativismPessimism(),
            'negativity_pessimism_experience' => $schema->getNegativismPessimismExperience(),
            'emotional_inhibition' => $schema->getEmotionalInhibition(),
            'emotional_inhibition_experience' => $schema->getEmotionalInhibitionExperience(),
            'unrelenting_standards' => $schema->getUnrelentingStandards(),
            'unrelenting_standards_experience' => $schema->getUnrelentingStandardsExperience(),
            'punitiveness' => $schema->getPunitiveness(),
            'punitiveness_experience' => $schema->getPunitivenessExperience(),
            'notes' => $schema->getNotes(),
            'created_at' => $schema->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $schema->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    /**
     * リクエストからDTOを生成
     */
    private function buildDataFromRequest($request): EarlyMaladaptiveSchemaData
    {
        return new EarlyMaladaptiveSchemaData(
            abandonment: $request->filled('abandonment') ? (int) $request->input('abandonment') : null,
            abandonmentExperience: $request->input('abandonment_experience'),
            mistrustAbuse: $request->filled('mistrust_abuse') ? (int) $request->input('mistrust_abuse') : null,
            mistrustAbuseExperience: $request->input('mistrust_abuse_experience'),
            emotionalDeprivation: $request->filled('emotional_deprivation') ? (int) $request->input('emotional_deprivation') : null,
            emotionalDeprivationExperience: $request->input('emotional_deprivation_experience'),
            defectivenessShame: $request->filled('defectiveness_shame') ? (int) $request->input('defectiveness_shame') : null,
            defectivenessShameExperience: $request->input('defectiveness_shame_experience'),
            socialIsolation: $request->filled('social_isolation') ? (int) $request->input('social_isolation') : null,
            socialIsolationExperience: $request->input('social_isolation_experience'),
            dependenceIncompetence: $request->filled('dependence_incompetence') ? (int) $request->input('dependence_incompetence') : null,
            dependenceIncompetenceExperience: $request->input('dependence_incompetence_experience'),
            vulnerabilityToHarm: $request->filled('vulnerability_to_harm') ? (int) $request->input('vulnerability_to_harm') : null,
            vulnerabilityToHarmExperience: $request->input('vulnerability_to_harm_experience'),
            enmeshment: $request->filled('enmeshment') ? (int) $request->input('enmeshment') : null,
            enmeshmentExperience: $request->input('enmeshment_experience'),
            failure: $request->filled('failure') ? (int) $request->input('failure') : null,
            failureExperience: $request->input('failure_experience'),
            entitlementGrandiosity: $request->filled('entitlement_grandiosity') ? (int) $request->input('entitlement_grandiosity') : null,
            entitlementGrandiosityExperience: $request->input('entitlement_grandiosity_experience'),
            insufficientSelfControl: $request->filled('insufficient_self_control') ? (int) $request->input('insufficient_self_control') : null,
            insufficientSelfControlExperience: $request->input('insufficient_self_control_experience'),
            subjugation: $request->filled('subjugation') ? (int) $request->input('subjugation') : null,
            subjugationExperience: $request->input('subjugation_experience'),
            selfSacrifice: $request->filled('self_sacrifice') ? (int) $request->input('self_sacrifice') : null,
            selfSacrificeExperience: $request->input('self_sacrifice_experience'),
            approvalSeeking: $request->filled('approval_seeking') ? (int) $request->input('approval_seeking') : null,
            approvalSeekingExperience: $request->input('approval_seeking_experience'),
            negativityPessimism: $request->filled('negativity_pessimism') ? (int) $request->input('negativity_pessimism') : null,
            negativityPessimismExperience: $request->input('negativity_pessimism_experience'),
            emotionalInhibition: $request->filled('emotional_inhibition') ? (int) $request->input('emotional_inhibition') : null,
            emotionalInhibitionExperience: $request->input('emotional_inhibition_experience'),
            unrelentingStandards: $request->filled('unrelenting_standards') ? (int) $request->input('unrelenting_standards') : null,
            unrelentingStandardsExperience: $request->input('unrelenting_standards_experience'),
            punitiveness: $request->filled('punitiveness') ? (int) $request->input('punitiveness') : null,
            punitivenessExperience: $request->input('punitiveness_experience'),
            notes: $request->input('notes')
        );
    }

    private function schemaToArray(EarlyMaladaptiveSchemaEntity $schema): array
    {
        return [
            'id' => $schema->getId(),
            'abandonment' => $schema->getAbandonment(),
            'abandonment_experience' => $schema->getAbandonmentExperience(),
            'mistrust_abuse' => $schema->getMistrustAbuse(),
            'mistrust_abuse_experience' => $schema->getMistrustAbuseExperience(),
            'emotional_deprivation' => $schema->getEmotionalDeprivation(),
            'emotional_deprivation_experience' => $schema->getEmotionalDeprivationExperience(),
            'defectiveness_shame' => $schema->getDefectivenessShame(),
            'defectiveness_shame_experience' => $schema->getDefectivenessShameExperience(),
            'social_isolation' => $schema->getSocialIsolation(),
            'social_isolation_experience' => $schema->getSocialIsolationExperience(),
            'dependence_incompetence' => $schema->getDependenceIncompetence(),
            'dependence_incompetence_experience' => $schema->getDependenceIncompetenceExperience(),
            'vulnerability_to_harm' => $schema->getVulnerabilityToHarm(),
            'vulnerability_to_harm_experience' => $schema->getVulnerabilityToHarmExperience(),
            'enmeshment' => $schema->getEnmeshment(),
            'enmeshment_experience' => $schema->getEnmeshmentExperience(),
            'failure' => $schema->getFailure(),
            'failure_experience' => $schema->getFailureExperience(),
            'entitlement_grandiosity' => $schema->getEntitlementGrandiosity(),
            'entitlement_grandiosity_experience' => $schema->getEntitlementGrandiosityExperience(),
            'insufficient_self_control' => $schema->getInsufficientSelfControl(),
            'insufficient_self_control_experience' => $schema->getInsufficientSelfControlExperience(),
            'subjugation' => $schema->getSubjugation(),
            'subjugation_experience' => $schema->getSubjugationExperience(),
            'self_sacrifice' => $schema->getSelfSacrifice(),
            'self_sacrifice_experience' => $schema->getSelfSacrificeExperience(),
            'approval_seeking' => $schema->getApprovalSeeking(),
            'approval_seeking_experience' => $schema->getApprovalSeekingExperience(),
            'negativity_pessimism' => $schema->getNegativismPessimism(),
            'negativity_pessimism_experience' => $schema->getNegativismPessimismExperience(),
            'emotional_inhibition' => $schema->getEmotionalInhibition(),
            'emotional_inhibition_experience' => $schema->getEmotionalInhibitionExperience(),
            'unrelenting_standards' => $schema->getUnrelentingStandards(),
            'unrelenting_standards_experience' => $schema->getUnrelentingStandardsExperience(),
            'punitiveness' => $schema->getPunitiveness(),
            'punitiveness_experience' => $schema->getPunitivenessExperience(),
            'notes' => $schema->getNotes(),
            'created_at' => $schema->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $schema->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}

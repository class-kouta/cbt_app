<?php

namespace App\Http\Controllers;

use App\Application\DTO\EarlyMaladaptiveSchemaData;
use App\Application\UseCase\EarlyMaladaptiveSchema\CreateEarlyMaladaptiveSchemaUseCase;
use App\Application\UseCase\EarlyMaladaptiveSchema\UpdateEarlyMaladaptiveSchemaUseCase;
use App\Http\Requests\EarlyMaladaptiveSchema\CreateEarlyMaladaptiveSchemaRequest;
use App\Http\Requests\EarlyMaladaptiveSchema\UpdateEarlyMaladaptiveSchemaRequest;
use App\Infrastructure\Database\Models\EarlyMaladaptiveSchema;
use Illuminate\Http\JsonResponse;

class EarlyMaladaptiveSchemaController extends Controller
{
    /**
     * 最初のスキーマを取得（存在しない場合は空のデータを返す）
     */
    public function show(): JsonResponse
    {
        $schema = EarlyMaladaptiveSchema::first();

        if ($schema === null) {
            return response()->json([
                'id' => null,
                'abandonment' => null,
                'mistrust_abuse' => null,
                'emotional_deprivation' => null,
                'defectiveness_shame' => null,
                'social_isolation' => null,
                'dependence_incompetence' => null,
                'vulnerability_to_harm' => null,
                'enmeshment' => null,
                'failure' => null,
                'entitlement_grandiosity' => null,
                'insufficient_self_control' => null,
                'subjugation' => null,
                'self_sacrifice' => null,
                'approval_seeking' => null,
                'negativity_pessimism' => null,
                'emotional_inhibition' => null,
                'unrelenting_standards' => null,
                'punitiveness' => null,
                'created_at' => null,
                'updated_at' => null,
            ]);
        }

        return response()->json([
            'id' => $schema->id,
            'abandonment' => $schema->abandonment,
            'mistrust_abuse' => $schema->mistrust_abuse,
            'emotional_deprivation' => $schema->emotional_deprivation,
            'defectiveness_shame' => $schema->defectiveness_shame,
            'social_isolation' => $schema->social_isolation,
            'dependence_incompetence' => $schema->dependence_incompetence,
            'vulnerability_to_harm' => $schema->vulnerability_to_harm,
            'enmeshment' => $schema->enmeshment,
            'failure' => $schema->failure,
            'entitlement_grandiosity' => $schema->entitlement_grandiosity,
            'insufficient_self_control' => $schema->insufficient_self_control,
            'subjugation' => $schema->subjugation,
            'self_sacrifice' => $schema->self_sacrifice,
            'approval_seeking' => $schema->approval_seeking,
            'negativity_pessimism' => $schema->negativity_pessimism,
            'emotional_inhibition' => $schema->emotional_inhibition,
            'unrelenting_standards' => $schema->unrelenting_standards,
            'punitiveness' => $schema->punitiveness,
            'created_at' => $schema->created_at->format(DATE_ATOM),
            'updated_at' => $schema->updated_at->format(DATE_ATOM),
        ]);
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
            'mistrust_abuse' => $schema->getMistrustAbuse(),
            'emotional_deprivation' => $schema->getEmotionalDeprivation(),
            'defectiveness_shame' => $schema->getDefectivenessShame(),
            'social_isolation' => $schema->getSocialIsolation(),
            'dependence_incompetence' => $schema->getDependenceIncompetence(),
            'vulnerability_to_harm' => $schema->getVulnerabilityToHarm(),
            'enmeshment' => $schema->getEnmeshment(),
            'failure' => $schema->getFailure(),
            'entitlement_grandiosity' => $schema->getEntitlementGrandiosity(),
            'insufficient_self_control' => $schema->getInsufficientSelfControl(),
            'subjugation' => $schema->getSubjugation(),
            'self_sacrifice' => $schema->getSelfSacrifice(),
            'approval_seeking' => $schema->getApprovalSeeking(),
            'negativity_pessimism' => $schema->getNegativismPessimism(),
            'emotional_inhibition' => $schema->getEmotionalInhibition(),
            'unrelenting_standards' => $schema->getUnrelentingStandards(),
            'punitiveness' => $schema->getPunitiveness(),
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
            'mistrust_abuse' => $schema->getMistrustAbuse(),
            'emotional_deprivation' => $schema->getEmotionalDeprivation(),
            'defectiveness_shame' => $schema->getDefectivenessShame(),
            'social_isolation' => $schema->getSocialIsolation(),
            'dependence_incompetence' => $schema->getDependenceIncompetence(),
            'vulnerability_to_harm' => $schema->getVulnerabilityToHarm(),
            'enmeshment' => $schema->getEnmeshment(),
            'failure' => $schema->getFailure(),
            'entitlement_grandiosity' => $schema->getEntitlementGrandiosity(),
            'insufficient_self_control' => $schema->getInsufficientSelfControl(),
            'subjugation' => $schema->getSubjugation(),
            'self_sacrifice' => $schema->getSelfSacrifice(),
            'approval_seeking' => $schema->getApprovalSeeking(),
            'negativity_pessimism' => $schema->getNegativismPessimism(),
            'emotional_inhibition' => $schema->getEmotionalInhibition(),
            'unrelenting_standards' => $schema->getUnrelentingStandards(),
            'punitiveness' => $schema->getPunitiveness(),
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
            mistrustAbuse: $request->filled('mistrust_abuse') ? (int) $request->input('mistrust_abuse') : null,
            emotionalDeprivation: $request->filled('emotional_deprivation') ? (int) $request->input('emotional_deprivation') : null,
            defectivenessShame: $request->filled('defectiveness_shame') ? (int) $request->input('defectiveness_shame') : null,
            socialIsolation: $request->filled('social_isolation') ? (int) $request->input('social_isolation') : null,
            dependenceIncompetence: $request->filled('dependence_incompetence') ? (int) $request->input('dependence_incompetence') : null,
            vulnerabilityToHarm: $request->filled('vulnerability_to_harm') ? (int) $request->input('vulnerability_to_harm') : null,
            enmeshment: $request->filled('enmeshment') ? (int) $request->input('enmeshment') : null,
            failure: $request->filled('failure') ? (int) $request->input('failure') : null,
            entitlementGrandiosity: $request->filled('entitlement_grandiosity') ? (int) $request->input('entitlement_grandiosity') : null,
            insufficientSelfControl: $request->filled('insufficient_self_control') ? (int) $request->input('insufficient_self_control') : null,
            subjugation: $request->filled('subjugation') ? (int) $request->input('subjugation') : null,
            selfSacrifice: $request->filled('self_sacrifice') ? (int) $request->input('self_sacrifice') : null,
            approvalSeeking: $request->filled('approval_seeking') ? (int) $request->input('approval_seeking') : null,
            negativityPessimism: $request->filled('negativity_pessimism') ? (int) $request->input('negativity_pessimism') : null,
            emotionalInhibition: $request->filled('emotional_inhibition') ? (int) $request->input('emotional_inhibition') : null,
            unrelentingStandards: $request->filled('unrelenting_standards') ? (int) $request->input('unrelenting_standards') : null,
            punitiveness: $request->filled('punitiveness') ? (int) $request->input('punitiveness') : null
        );
    }
}

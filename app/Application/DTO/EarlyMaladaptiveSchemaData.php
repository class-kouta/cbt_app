<?php

namespace App\Application\DTO;

readonly class EarlyMaladaptiveSchemaData
{
    public function __construct(
        // 第1領域：切断と拒絶
        public ?int $abandonment,
        public ?string $abandonmentExperience,
        public ?int $mistrustAbuse,
        public ?string $mistrustAbuseExperience,
        public ?int $emotionalDeprivation,
        public ?string $emotionalDeprivationExperience,
        public ?int $defectivenessShame,
        public ?string $defectivenessShameExperience,
        public ?int $socialIsolation,
        public ?string $socialIsolationExperience,
        
        // 第2領域：自律性と機能の障害
        public ?int $dependenceIncompetence,
        public ?string $dependenceIncompetenceExperience,
        public ?int $vulnerabilityToHarm,
        public ?string $vulnerabilityToHarmExperience,
        public ?int $enmeshment,
        public ?string $enmeshmentExperience,
        public ?int $failure,
        public ?string $failureExperience,
        
        // 第3領域：制約の欠如
        public ?int $entitlementGrandiosity,
        public ?string $entitlementGrandiosityExperience,
        public ?int $insufficientSelfControl,
        public ?string $insufficientSelfControlExperience,
        
        // 第4領域：他者への志向
        public ?int $subjugation,
        public ?string $subjugationExperience,
        public ?int $selfSacrifice,
        public ?string $selfSacrificeExperience,
        public ?int $approvalSeeking,
        public ?string $approvalSeekingExperience,
        
        // 第5領域：過剰警戒と抑制
        public ?int $negativityPessimism,
        public ?string $negativityPessimismExperience,
        public ?int $emotionalInhibition,
        public ?string $emotionalInhibitionExperience,
        public ?int $unrelentingStandards,
        public ?string $unrelentingStandardsExperience,
        public ?int $punitiveness,
        public ?string $punitivenessExperience,
        
        // 備考欄
        public ?string $notes
    ) {
    }
}

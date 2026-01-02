<?php

namespace App\Application\DTO;

readonly class EarlyMaladaptiveSchemaData
{
    public function __construct(
        // 第1領域：切断と拒絶
        public ?int $abandonment,
        public ?int $mistrustAbuse,
        public ?int $emotionalDeprivation,
        public ?int $defectivenessShame,
        public ?int $socialIsolation,
        
        // 第2領域：自律性と機能の障害
        public ?int $dependenceIncompetence,
        public ?int $vulnerabilityToHarm,
        public ?int $enmeshment,
        public ?int $failure,
        
        // 第3領域：制約の欠如
        public ?int $entitlementGrandiosity,
        public ?int $insufficientSelfControl,
        
        // 第4領域：他者への志向
        public ?int $subjugation,
        public ?int $selfSacrifice,
        public ?int $approvalSeeking,
        
        // 第5領域：過剰警戒と抑制
        public ?int $negativityPessimism,
        public ?int $emotionalInhibition,
        public ?int $unrelentingStandards,
        public ?int $punitiveness
    ) {
    }
}

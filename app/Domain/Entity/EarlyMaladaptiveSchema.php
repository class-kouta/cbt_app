<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class EarlyMaladaptiveSchema
{
    private ?int $id;
    
    // 第1領域：切断と拒絶
    private ?int $abandonment;
    private ?int $mistrustAbuse;
    private ?int $emotionalDeprivation;
    private ?int $defectivenessShame;
    private ?int $socialIsolation;
    
    // 第2領域：自律性と機能の障害
    private ?int $dependenceIncompetence;
    private ?int $vulnerabilityToHarm;
    private ?int $enmeshment;
    private ?int $failure;
    
    // 第3領域：制約の欠如
    private ?int $entitlementGrandiosity;
    private ?int $insufficientSelfControl;
    
    // 第4領域：他者への志向
    private ?int $subjugation;
    private ?int $selfSacrifice;
    private ?int $approvalSeeking;
    
    // 第5領域：過剰警戒と抑制
    private ?int $negativityPessimism;
    private ?int $emotionalInhibition;
    private ?int $unrelentingStandards;
    private ?int $punitiveness;
    
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ?int $abandonment,
        ?int $mistrustAbuse,
        ?int $emotionalDeprivation,
        ?int $defectivenessShame,
        ?int $socialIsolation,
        ?int $dependenceIncompetence,
        ?int $vulnerabilityToHarm,
        ?int $enmeshment,
        ?int $failure,
        ?int $entitlementGrandiosity,
        ?int $insufficientSelfControl,
        ?int $subjugation,
        ?int $selfSacrifice,
        ?int $approvalSeeking,
        ?int $negativityPessimism,
        ?int $emotionalInhibition,
        ?int $unrelentingStandards,
        ?int $punitiveness,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->abandonment = $abandonment;
        $this->mistrustAbuse = $mistrustAbuse;
        $this->emotionalDeprivation = $emotionalDeprivation;
        $this->defectivenessShame = $defectivenessShame;
        $this->socialIsolation = $socialIsolation;
        $this->dependenceIncompetence = $dependenceIncompetence;
        $this->vulnerabilityToHarm = $vulnerabilityToHarm;
        $this->enmeshment = $enmeshment;
        $this->failure = $failure;
        $this->entitlementGrandiosity = $entitlementGrandiosity;
        $this->insufficientSelfControl = $insufficientSelfControl;
        $this->subjugation = $subjugation;
        $this->selfSacrifice = $selfSacrifice;
        $this->approvalSeeking = $approvalSeeking;
        $this->negativityPessimism = $negativityPessimism;
        $this->emotionalInhibition = $emotionalInhibition;
        $this->unrelentingStandards = $unrelentingStandards;
        $this->punitiveness = $punitiveness;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        ?int $abandonment,
        ?int $mistrustAbuse,
        ?int $emotionalDeprivation,
        ?int $defectivenessShame,
        ?int $socialIsolation,
        ?int $dependenceIncompetence,
        ?int $vulnerabilityToHarm,
        ?int $enmeshment,
        ?int $failure,
        ?int $entitlementGrandiosity,
        ?int $insufficientSelfControl,
        ?int $subjugation,
        ?int $selfSacrifice,
        ?int $approvalSeeking,
        ?int $negativityPessimism,
        ?int $emotionalInhibition,
        ?int $unrelentingStandards,
        ?int $punitiveness
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            $abandonment,
            $mistrustAbuse,
            $emotionalDeprivation,
            $defectivenessShame,
            $socialIsolation,
            $dependenceIncompetence,
            $vulnerabilityToHarm,
            $enmeshment,
            $failure,
            $entitlementGrandiosity,
            $insufficientSelfControl,
            $subjugation,
            $selfSacrifice,
            $approvalSeeking,
            $negativityPessimism,
            $emotionalInhibition,
            $unrelentingStandards,
            $punitiveness,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        ?int $abandonment,
        ?int $mistrustAbuse,
        ?int $emotionalDeprivation,
        ?int $defectivenessShame,
        ?int $socialIsolation,
        ?int $dependenceIncompetence,
        ?int $vulnerabilityToHarm,
        ?int $enmeshment,
        ?int $failure,
        ?int $entitlementGrandiosity,
        ?int $insufficientSelfControl,
        ?int $subjugation,
        ?int $selfSacrifice,
        ?int $approvalSeeking,
        ?int $negativityPessimism,
        ?int $emotionalInhibition,
        ?int $unrelentingStandards,
        ?int $punitiveness,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $abandonment,
            $mistrustAbuse,
            $emotionalDeprivation,
            $defectivenessShame,
            $socialIsolation,
            $dependenceIncompetence,
            $vulnerabilityToHarm,
            $enmeshment,
            $failure,
            $entitlementGrandiosity,
            $insufficientSelfControl,
            $subjugation,
            $selfSacrifice,
            $approvalSeeking,
            $negativityPessimism,
            $emotionalInhibition,
            $unrelentingStandards,
            $punitiveness,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbandonment(): ?int
    {
        return $this->abandonment;
    }

    public function getMistrustAbuse(): ?int
    {
        return $this->mistrustAbuse;
    }

    public function getEmotionalDeprivation(): ?int
    {
        return $this->emotionalDeprivation;
    }

    public function getDefectivenessShame(): ?int
    {
        return $this->defectivenessShame;
    }

    public function getSocialIsolation(): ?int
    {
        return $this->socialIsolation;
    }

    public function getDependenceIncompetence(): ?int
    {
        return $this->dependenceIncompetence;
    }

    public function getVulnerabilityToHarm(): ?int
    {
        return $this->vulnerabilityToHarm;
    }

    public function getEnmeshment(): ?int
    {
        return $this->enmeshment;
    }

    public function getFailure(): ?int
    {
        return $this->failure;
    }

    public function getEntitlementGrandiosity(): ?int
    {
        return $this->entitlementGrandiosity;
    }

    public function getInsufficientSelfControl(): ?int
    {
        return $this->insufficientSelfControl;
    }

    public function getSubjugation(): ?int
    {
        return $this->subjugation;
    }

    public function getSelfSacrifice(): ?int
    {
        return $this->selfSacrifice;
    }

    public function getApprovalSeeking(): ?int
    {
        return $this->approvalSeeking;
    }

    public function getNegativismPessimism(): ?int
    {
        return $this->negativityPessimism;
    }

    public function getEmotionalInhibition(): ?int
    {
        return $this->emotionalInhibition;
    }

    public function getUnrelentingStandards(): ?int
    {
        return $this->unrelentingStandards;
    }

    public function getPunitiveness(): ?int
    {
        return $this->punitiveness;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->abandonment,
            $this->mistrustAbuse,
            $this->emotionalDeprivation,
            $this->defectivenessShame,
            $this->socialIsolation,
            $this->dependenceIncompetence,
            $this->vulnerabilityToHarm,
            $this->enmeshment,
            $this->failure,
            $this->entitlementGrandiosity,
            $this->insufficientSelfControl,
            $this->subjugation,
            $this->selfSacrifice,
            $this->approvalSeeking,
            $this->negativityPessimism,
            $this->emotionalInhibition,
            $this->unrelentingStandards,
            $this->punitiveness,
            $this->createdAt,
            $this->updatedAt
        );
    }
}

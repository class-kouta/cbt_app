<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class EarlyMaladaptiveSchema
{
    private ?int $id;
    
    // 第1領域：切断と拒絶
    private ?int $abandonment;
    private ?string $abandonmentExperience;
    private ?int $mistrustAbuse;
    private ?string $mistrustAbuseExperience;
    private ?int $emotionalDeprivation;
    private ?string $emotionalDeprivationExperience;
    private ?int $defectivenessShame;
    private ?string $defectivenessShameExperience;
    private ?int $socialIsolation;
    private ?string $socialIsolationExperience;
    
    // 第2領域：自律性と機能の障害
    private ?int $dependenceIncompetence;
    private ?string $dependenceIncompetenceExperience;
    private ?int $vulnerabilityToHarm;
    private ?string $vulnerabilityToHarmExperience;
    private ?int $enmeshment;
    private ?string $enmeshmentExperience;
    private ?int $failure;
    private ?string $failureExperience;
    
    // 第3領域：制約の欠如
    private ?int $entitlementGrandiosity;
    private ?string $entitlementGrandiosityExperience;
    private ?int $insufficientSelfControl;
    private ?string $insufficientSelfControlExperience;
    
    // 第4領域：他者への志向
    private ?int $subjugation;
    private ?string $subjugationExperience;
    private ?int $selfSacrifice;
    private ?string $selfSacrificeExperience;
    private ?int $approvalSeeking;
    private ?string $approvalSeekingExperience;
    
    // 第5領域：過剰警戒と抑制
    private ?int $negativityPessimism;
    private ?string $negativityPessimismExperience;
    private ?int $emotionalInhibition;
    private ?string $emotionalInhibitionExperience;
    private ?int $unrelentingStandards;
    private ?string $unrelentingStandardsExperience;
    private ?int $punitiveness;
    private ?string $punitivenessExperience;
    
    // 備考欄
    private ?string $notes;
    
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ?int $abandonment,
        ?string $abandonmentExperience,
        ?int $mistrustAbuse,
        ?string $mistrustAbuseExperience,
        ?int $emotionalDeprivation,
        ?string $emotionalDeprivationExperience,
        ?int $defectivenessShame,
        ?string $defectivenessShameExperience,
        ?int $socialIsolation,
        ?string $socialIsolationExperience,
        ?int $dependenceIncompetence,
        ?string $dependenceIncompetenceExperience,
        ?int $vulnerabilityToHarm,
        ?string $vulnerabilityToHarmExperience,
        ?int $enmeshment,
        ?string $enmeshmentExperience,
        ?int $failure,
        ?string $failureExperience,
        ?int $entitlementGrandiosity,
        ?string $entitlementGrandiosityExperience,
        ?int $insufficientSelfControl,
        ?string $insufficientSelfControlExperience,
        ?int $subjugation,
        ?string $subjugationExperience,
        ?int $selfSacrifice,
        ?string $selfSacrificeExperience,
        ?int $approvalSeeking,
        ?string $approvalSeekingExperience,
        ?int $negativityPessimism,
        ?string $negativityPessimismExperience,
        ?int $emotionalInhibition,
        ?string $emotionalInhibitionExperience,
        ?int $unrelentingStandards,
        ?string $unrelentingStandardsExperience,
        ?int $punitiveness,
        ?string $punitivenessExperience,
        ?string $notes,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->abandonment = $abandonment;
        $this->abandonmentExperience = $abandonmentExperience;
        $this->mistrustAbuse = $mistrustAbuse;
        $this->mistrustAbuseExperience = $mistrustAbuseExperience;
        $this->emotionalDeprivation = $emotionalDeprivation;
        $this->emotionalDeprivationExperience = $emotionalDeprivationExperience;
        $this->defectivenessShame = $defectivenessShame;
        $this->defectivenessShameExperience = $defectivenessShameExperience;
        $this->socialIsolation = $socialIsolation;
        $this->socialIsolationExperience = $socialIsolationExperience;
        $this->dependenceIncompetence = $dependenceIncompetence;
        $this->dependenceIncompetenceExperience = $dependenceIncompetenceExperience;
        $this->vulnerabilityToHarm = $vulnerabilityToHarm;
        $this->vulnerabilityToHarmExperience = $vulnerabilityToHarmExperience;
        $this->enmeshment = $enmeshment;
        $this->enmeshmentExperience = $enmeshmentExperience;
        $this->failure = $failure;
        $this->failureExperience = $failureExperience;
        $this->entitlementGrandiosity = $entitlementGrandiosity;
        $this->entitlementGrandiosityExperience = $entitlementGrandiosityExperience;
        $this->insufficientSelfControl = $insufficientSelfControl;
        $this->insufficientSelfControlExperience = $insufficientSelfControlExperience;
        $this->subjugation = $subjugation;
        $this->subjugationExperience = $subjugationExperience;
        $this->selfSacrifice = $selfSacrifice;
        $this->selfSacrificeExperience = $selfSacrificeExperience;
        $this->approvalSeeking = $approvalSeeking;
        $this->approvalSeekingExperience = $approvalSeekingExperience;
        $this->negativityPessimism = $negativityPessimism;
        $this->negativityPessimismExperience = $negativityPessimismExperience;
        $this->emotionalInhibition = $emotionalInhibition;
        $this->emotionalInhibitionExperience = $emotionalInhibitionExperience;
        $this->unrelentingStandards = $unrelentingStandards;
        $this->unrelentingStandardsExperience = $unrelentingStandardsExperience;
        $this->punitiveness = $punitiveness;
        $this->punitivenessExperience = $punitivenessExperience;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        ?int $abandonment,
        ?string $abandonmentExperience,
        ?int $mistrustAbuse,
        ?string $mistrustAbuseExperience,
        ?int $emotionalDeprivation,
        ?string $emotionalDeprivationExperience,
        ?int $defectivenessShame,
        ?string $defectivenessShameExperience,
        ?int $socialIsolation,
        ?string $socialIsolationExperience,
        ?int $dependenceIncompetence,
        ?string $dependenceIncompetenceExperience,
        ?int $vulnerabilityToHarm,
        ?string $vulnerabilityToHarmExperience,
        ?int $enmeshment,
        ?string $enmeshmentExperience,
        ?int $failure,
        ?string $failureExperience,
        ?int $entitlementGrandiosity,
        ?string $entitlementGrandiosityExperience,
        ?int $insufficientSelfControl,
        ?string $insufficientSelfControlExperience,
        ?int $subjugation,
        ?string $subjugationExperience,
        ?int $selfSacrifice,
        ?string $selfSacrificeExperience,
        ?int $approvalSeeking,
        ?string $approvalSeekingExperience,
        ?int $negativityPessimism,
        ?string $negativityPessimismExperience,
        ?int $emotionalInhibition,
        ?string $emotionalInhibitionExperience,
        ?int $unrelentingStandards,
        ?string $unrelentingStandardsExperience,
        ?int $punitiveness,
        ?string $punitivenessExperience,
        ?string $notes
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            $abandonment,
            $abandonmentExperience,
            $mistrustAbuse,
            $mistrustAbuseExperience,
            $emotionalDeprivation,
            $emotionalDeprivationExperience,
            $defectivenessShame,
            $defectivenessShameExperience,
            $socialIsolation,
            $socialIsolationExperience,
            $dependenceIncompetence,
            $dependenceIncompetenceExperience,
            $vulnerabilityToHarm,
            $vulnerabilityToHarmExperience,
            $enmeshment,
            $enmeshmentExperience,
            $failure,
            $failureExperience,
            $entitlementGrandiosity,
            $entitlementGrandiosityExperience,
            $insufficientSelfControl,
            $insufficientSelfControlExperience,
            $subjugation,
            $subjugationExperience,
            $selfSacrifice,
            $selfSacrificeExperience,
            $approvalSeeking,
            $approvalSeekingExperience,
            $negativityPessimism,
            $negativityPessimismExperience,
            $emotionalInhibition,
            $emotionalInhibitionExperience,
            $unrelentingStandards,
            $unrelentingStandardsExperience,
            $punitiveness,
            $punitivenessExperience,
            $notes,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        ?int $abandonment,
        ?string $abandonmentExperience,
        ?int $mistrustAbuse,
        ?string $mistrustAbuseExperience,
        ?int $emotionalDeprivation,
        ?string $emotionalDeprivationExperience,
        ?int $defectivenessShame,
        ?string $defectivenessShameExperience,
        ?int $socialIsolation,
        ?string $socialIsolationExperience,
        ?int $dependenceIncompetence,
        ?string $dependenceIncompetenceExperience,
        ?int $vulnerabilityToHarm,
        ?string $vulnerabilityToHarmExperience,
        ?int $enmeshment,
        ?string $enmeshmentExperience,
        ?int $failure,
        ?string $failureExperience,
        ?int $entitlementGrandiosity,
        ?string $entitlementGrandiosityExperience,
        ?int $insufficientSelfControl,
        ?string $insufficientSelfControlExperience,
        ?int $subjugation,
        ?string $subjugationExperience,
        ?int $selfSacrifice,
        ?string $selfSacrificeExperience,
        ?int $approvalSeeking,
        ?string $approvalSeekingExperience,
        ?int $negativityPessimism,
        ?string $negativityPessimismExperience,
        ?int $emotionalInhibition,
        ?string $emotionalInhibitionExperience,
        ?int $unrelentingStandards,
        ?string $unrelentingStandardsExperience,
        ?int $punitiveness,
        ?string $punitivenessExperience,
        ?string $notes,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $abandonment,
            $abandonmentExperience,
            $mistrustAbuse,
            $mistrustAbuseExperience,
            $emotionalDeprivation,
            $emotionalDeprivationExperience,
            $defectivenessShame,
            $defectivenessShameExperience,
            $socialIsolation,
            $socialIsolationExperience,
            $dependenceIncompetence,
            $dependenceIncompetenceExperience,
            $vulnerabilityToHarm,
            $vulnerabilityToHarmExperience,
            $enmeshment,
            $enmeshmentExperience,
            $failure,
            $failureExperience,
            $entitlementGrandiosity,
            $entitlementGrandiosityExperience,
            $insufficientSelfControl,
            $insufficientSelfControlExperience,
            $subjugation,
            $subjugationExperience,
            $selfSacrifice,
            $selfSacrificeExperience,
            $approvalSeeking,
            $approvalSeekingExperience,
            $negativityPessimism,
            $negativityPessimismExperience,
            $emotionalInhibition,
            $emotionalInhibitionExperience,
            $unrelentingStandards,
            $unrelentingStandardsExperience,
            $punitiveness,
            $punitivenessExperience,
            $notes,
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

    public function getAbandonmentExperience(): ?string
    {
        return $this->abandonmentExperience;
    }

    public function getMistrustAbuse(): ?int
    {
        return $this->mistrustAbuse;
    }

    public function getMistrustAbuseExperience(): ?string
    {
        return $this->mistrustAbuseExperience;
    }

    public function getEmotionalDeprivation(): ?int
    {
        return $this->emotionalDeprivation;
    }

    public function getEmotionalDeprivationExperience(): ?string
    {
        return $this->emotionalDeprivationExperience;
    }

    public function getDefectivenessShame(): ?int
    {
        return $this->defectivenessShame;
    }

    public function getDefectivenessShameExperience(): ?string
    {
        return $this->defectivenessShameExperience;
    }

    public function getSocialIsolation(): ?int
    {
        return $this->socialIsolation;
    }

    public function getSocialIsolationExperience(): ?string
    {
        return $this->socialIsolationExperience;
    }

    public function getDependenceIncompetence(): ?int
    {
        return $this->dependenceIncompetence;
    }

    public function getDependenceIncompetenceExperience(): ?string
    {
        return $this->dependenceIncompetenceExperience;
    }

    public function getVulnerabilityToHarm(): ?int
    {
        return $this->vulnerabilityToHarm;
    }

    public function getVulnerabilityToHarmExperience(): ?string
    {
        return $this->vulnerabilityToHarmExperience;
    }

    public function getEnmeshment(): ?int
    {
        return $this->enmeshment;
    }

    public function getEnmeshmentExperience(): ?string
    {
        return $this->enmeshmentExperience;
    }

    public function getFailure(): ?int
    {
        return $this->failure;
    }

    public function getFailureExperience(): ?string
    {
        return $this->failureExperience;
    }

    public function getEntitlementGrandiosity(): ?int
    {
        return $this->entitlementGrandiosity;
    }

    public function getEntitlementGrandiosityExperience(): ?string
    {
        return $this->entitlementGrandiosityExperience;
    }

    public function getInsufficientSelfControl(): ?int
    {
        return $this->insufficientSelfControl;
    }

    public function getInsufficientSelfControlExperience(): ?string
    {
        return $this->insufficientSelfControlExperience;
    }

    public function getSubjugation(): ?int
    {
        return $this->subjugation;
    }

    public function getSubjugationExperience(): ?string
    {
        return $this->subjugationExperience;
    }

    public function getSelfSacrifice(): ?int
    {
        return $this->selfSacrifice;
    }

    public function getSelfSacrificeExperience(): ?string
    {
        return $this->selfSacrificeExperience;
    }

    public function getApprovalSeeking(): ?int
    {
        return $this->approvalSeeking;
    }

    public function getApprovalSeekingExperience(): ?string
    {
        return $this->approvalSeekingExperience;
    }

    public function getNegativismPessimism(): ?int
    {
        return $this->negativityPessimism;
    }

    public function getNegativismPessimismExperience(): ?string
    {
        return $this->negativityPessimismExperience;
    }

    public function getEmotionalInhibition(): ?int
    {
        return $this->emotionalInhibition;
    }

    public function getEmotionalInhibitionExperience(): ?string
    {
        return $this->emotionalInhibitionExperience;
    }

    public function getUnrelentingStandards(): ?int
    {
        return $this->unrelentingStandards;
    }

    public function getUnrelentingStandardsExperience(): ?string
    {
        return $this->unrelentingStandardsExperience;
    }

    public function getPunitiveness(): ?int
    {
        return $this->punitiveness;
    }

    public function getPunitivenessExperience(): ?string
    {
        return $this->punitivenessExperience;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
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
            $this->abandonmentExperience,
            $this->mistrustAbuse,
            $this->mistrustAbuseExperience,
            $this->emotionalDeprivation,
            $this->emotionalDeprivationExperience,
            $this->defectivenessShame,
            $this->defectivenessShameExperience,
            $this->socialIsolation,
            $this->socialIsolationExperience,
            $this->dependenceIncompetence,
            $this->dependenceIncompetenceExperience,
            $this->vulnerabilityToHarm,
            $this->vulnerabilityToHarmExperience,
            $this->enmeshment,
            $this->enmeshmentExperience,
            $this->failure,
            $this->failureExperience,
            $this->entitlementGrandiosity,
            $this->entitlementGrandiosityExperience,
            $this->insufficientSelfControl,
            $this->insufficientSelfControlExperience,
            $this->subjugation,
            $this->subjugationExperience,
            $this->selfSacrifice,
            $this->selfSacrificeExperience,
            $this->approvalSeeking,
            $this->approvalSeekingExperience,
            $this->negativityPessimism,
            $this->negativityPessimismExperience,
            $this->emotionalInhibition,
            $this->emotionalInhibitionExperience,
            $this->unrelentingStandards,
            $this->unrelentingStandardsExperience,
            $this->punitiveness,
            $this->punitivenessExperience,
            $this->notes,
            $this->createdAt,
            $this->updatedAt
        );
    }
}

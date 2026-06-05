<?php

namespace App\Infrastructure\Database\Models;

use App\Infrastructure\Database\Models\Concerns\BelongsToAuthenticatedMember;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EarlyMaladaptiveSchema extends Model
{
    use BelongsToAuthenticatedMember;
    use HasFactory;

    protected $fillable = [
        'member_id',
        // 第1領域：切断と拒絶
        'abandonment',
        'abandonment_experience',
        'mistrust_abuse',
        'mistrust_abuse_experience',
        'emotional_deprivation',
        'emotional_deprivation_experience',
        'defectiveness_shame',
        'defectiveness_shame_experience',
        'social_isolation',
        'social_isolation_experience',
        
        // 第2領域：自律性と機能の障害
        'dependence_incompetence',
        'dependence_incompetence_experience',
        'vulnerability_to_harm',
        'vulnerability_to_harm_experience',
        'enmeshment',
        'enmeshment_experience',
        'failure',
        'failure_experience',
        
        // 第3領域：制約の欠如
        'entitlement_grandiosity',
        'entitlement_grandiosity_experience',
        'insufficient_self_control',
        'insufficient_self_control_experience',
        
        // 第4領域：他者への志向
        'subjugation',
        'subjugation_experience',
        'self_sacrifice',
        'self_sacrifice_experience',
        'approval_seeking',
        'approval_seeking_experience',
        
        // 第5領域：過剰警戒と抑制
        'negativity_pessimism',
        'negativity_pessimism_experience',
        'emotional_inhibition',
        'emotional_inhibition_experience',
        'unrelenting_standards',
        'unrelenting_standards_experience',
        'punitiveness',
        'punitiveness_experience',
        
        // 備考欄
        'notes',
    ];

    protected $casts = [
        'abandonment' => 'integer',
        'mistrust_abuse' => 'integer',
        'emotional_deprivation' => 'integer',
        'defectiveness_shame' => 'integer',
        'social_isolation' => 'integer',
        'dependence_incompetence' => 'integer',
        'vulnerability_to_harm' => 'integer',
        'enmeshment' => 'integer',
        'failure' => 'integer',
        'entitlement_grandiosity' => 'integer',
        'insufficient_self_control' => 'integer',
        'subjugation' => 'integer',
        'self_sacrifice' => 'integer',
        'approval_seeking' => 'integer',
        'negativity_pessimism' => 'integer',
        'emotional_inhibition' => 'integer',
        'unrelenting_standards' => 'integer',
        'punitiveness' => 'integer',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}

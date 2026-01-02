<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarlyMaladaptiveSchema extends Model
{
    use HasFactory;

    protected $fillable = [
        // 第1領域：切断と拒絶
        'abandonment',
        'mistrust_abuse',
        'emotional_deprivation',
        'defectiveness_shame',
        'social_isolation',
        
        // 第2領域：自律性と機能の障害
        'dependence_incompetence',
        'vulnerability_to_harm',
        'enmeshment',
        'failure',
        
        // 第3領域：制約の欠如
        'entitlement_grandiosity',
        'insufficient_self_control',
        
        // 第4領域：他者への志向
        'subjugation',
        'self_sacrifice',
        'approval_seeking',
        
        // 第5領域：過剰警戒と抑制
        'negativity_pessimism',
        'emotional_inhibition',
        'unrelenting_standards',
        'punitiveness',
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
}

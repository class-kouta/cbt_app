<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('early_maladaptive_schemas', function (Blueprint $table) {
            // 第1領域：切断と拒絶（経験入力欄）
            $table->text('abandonment_experience')->nullable()->after('abandonment');
            $table->text('mistrust_abuse_experience')->nullable()->after('mistrust_abuse');
            $table->text('emotional_deprivation_experience')->nullable()->after('emotional_deprivation');
            $table->text('defectiveness_shame_experience')->nullable()->after('defectiveness_shame');
            $table->text('social_isolation_experience')->nullable()->after('social_isolation');
            
            // 第2領域：自律性と機能の障害（経験入力欄）
            $table->text('dependence_incompetence_experience')->nullable()->after('dependence_incompetence');
            $table->text('vulnerability_to_harm_experience')->nullable()->after('vulnerability_to_harm');
            $table->text('enmeshment_experience')->nullable()->after('enmeshment');
            $table->text('failure_experience')->nullable()->after('failure');
            
            // 第3領域：制約の欠如（経験入力欄）
            $table->text('entitlement_grandiosity_experience')->nullable()->after('entitlement_grandiosity');
            $table->text('insufficient_self_control_experience')->nullable()->after('insufficient_self_control');
            
            // 第4領域：他者への志向（経験入力欄）
            $table->text('subjugation_experience')->nullable()->after('subjugation');
            $table->text('self_sacrifice_experience')->nullable()->after('self_sacrifice');
            $table->text('approval_seeking_experience')->nullable()->after('approval_seeking');
            
            // 第5領域：過剰警戒と抑制（経験入力欄）
            $table->text('negativity_pessimism_experience')->nullable()->after('negativity_pessimism');
            $table->text('emotional_inhibition_experience')->nullable()->after('emotional_inhibition');
            $table->text('unrelenting_standards_experience')->nullable()->after('unrelenting_standards');
            $table->text('punitiveness_experience')->nullable()->after('punitiveness');
            
            // 備考欄
            $table->text('notes')->nullable()->after('punitiveness_experience');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('early_maladaptive_schemas', function (Blueprint $table) {
            $table->dropColumn([
                'abandonment_experience',
                'mistrust_abuse_experience',
                'emotional_deprivation_experience',
                'defectiveness_shame_experience',
                'social_isolation_experience',
                'dependence_incompetence_experience',
                'vulnerability_to_harm_experience',
                'enmeshment_experience',
                'failure_experience',
                'entitlement_grandiosity_experience',
                'insufficient_self_control_experience',
                'subjugation_experience',
                'self_sacrifice_experience',
                'approval_seeking_experience',
                'negativity_pessimism_experience',
                'emotional_inhibition_experience',
                'unrelenting_standards_experience',
                'punitiveness_experience',
                'notes',
            ]);
        });
    }
};

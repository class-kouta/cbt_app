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
        Schema::create('early_maladaptive_schemas', function (Blueprint $table) {
            $table->id();
            
            // 第1領域：切断と拒絶
            $table->tinyInteger('abandonment')->nullable();           // 1. 見捨てられ/不安定スキーマ (0-100%)
            $table->tinyInteger('mistrust_abuse')->nullable();        // 2. 不信/虐待スキーマ (0-100%)
            $table->tinyInteger('emotional_deprivation')->nullable(); // 3. 情緒的剥奪スキーマ (0-100%)
            $table->tinyInteger('defectiveness_shame')->nullable();   // 4. 欠陥/恥スキーマ (0-100%)
            $table->tinyInteger('social_isolation')->nullable();      // 5. 社会的孤立/疎外スキーマ (0-100%)
            
            // 第2領域：自律性と機能の障害
            $table->tinyInteger('dependence_incompetence')->nullable();    // 6. 依存/無能スキーマ (0-100%)
            $table->tinyInteger('vulnerability_to_harm')->nullable();      // 7. 損害や疾病に対する脆弱性スキーマ (0-100%)
            $table->tinyInteger('enmeshment')->nullable();                 // 8. 巻き込まれ/未発達な自己スキーマ (0-100%)
            $table->tinyInteger('failure')->nullable();                    // 9. 失敗スキーマ (0-100%)
            
            // 第3領域：制約の欠如
            $table->tinyInteger('entitlement_grandiosity')->nullable();    // 10. 権利要求/尊大さスキーマ (0-100%)
            $table->tinyInteger('insufficient_self_control')->nullable(); // 11. 自制と自律の欠如スキーマ (0-100%)
            
            // 第4領域：他者への志向
            $table->tinyInteger('subjugation')->nullable();               // 12. 服従スキーマ (0-100%)
            $table->tinyInteger('self_sacrifice')->nullable();            // 13. 自己犠牲スキーマ (0-100%)
            $table->tinyInteger('approval_seeking')->nullable();          // 14. 承認欲求/評価の追求スキーマ (0-100%)
            
            // 第5領域：過剰警戒と抑制
            $table->tinyInteger('negativity_pessimism')->nullable();      // 15. 否定/悲観スキーマ (0-100%)
            $table->tinyInteger('emotional_inhibition')->nullable();      // 16. 感情抑制スキーマ (0-100%)
            $table->tinyInteger('unrelenting_standards')->nullable();     // 17. 厳密な基準/過度の批判スキーマ (0-100%)
            $table->tinyInteger('punitiveness')->nullable();              // 18. 罰への懲罰的志向スキーマ (0-100%)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('early_maladaptive_schemas');
    }
};

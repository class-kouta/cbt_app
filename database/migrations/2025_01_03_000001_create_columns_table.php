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
        Schema::create('columns', function (Blueprint $table) {
            $table->id();
            $table->text('situation');              // 状況：気持ちが動揺したときの一場面
            $table->text('mood');                   // 気分：そのときの気持ち
            $table->text('automatic_thought');      // 自動思考
            $table->text('evidence');               // 根拠：自動思考を裏付ける具体的な事実
            $table->text('counter_evidence');       // 反証：自動思考と反対の事実
            $table->text('adaptive_thought');       // 適応的思考：バランスのよい考え
            $table->text('current_mood');           // いまの気分
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('columns');
    }
};

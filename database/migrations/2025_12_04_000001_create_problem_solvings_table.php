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
        Schema::create('problem_solvings', function (Blueprint $table) {
            $table->id();
            $table->text('problem_situation');      // 問題状況（Step 1）※必須
            $table->text('self_talk')->nullable();  // 自分への声かけ（Step 2）
            $table->text('improved_image')->nullable(); // 改善イメージ（Step 3）
            $table->text('action_plan')->nullable(); // 実行計画（Step 5）
            $table->text('reflection')->nullable(); // 振り返り（Step 6）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_solvings');
    }
};

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
        Schema::create('problem_solving_solutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_solving_id')
                ->constrained('problem_solvings')
                ->cascadeOnDelete();
            $table->text('content');                    // 解決策の内容
            $table->unsignedTinyInteger('effectiveness')->nullable(); // 効果的か（0-100%）
            $table->unsignedTinyInteger('feasibility')->nullable();   // 実行可能か（0-100%）
            $table->integer('sort_order');              // 表示順（1-7）
            $table->timestamps();

            $table->index(['problem_solving_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_solving_solutions');
    }
};

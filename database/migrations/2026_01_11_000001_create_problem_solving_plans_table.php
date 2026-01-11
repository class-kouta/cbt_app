<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('problem_solving_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_solving_id')
                ->constrained('problem_solvings')
                ->cascadeOnDelete();
            $table->unsignedInteger('plan_number')->default(1); // 計画番号（1から始まる）
            $table->text('action_plan')->nullable();            // 実行計画
            $table->text('reflection')->nullable();             // 振り返り
            $table->timestamps();

            $table->unique(['problem_solving_id', 'plan_number']);
            $table->index('problem_solving_id');
        });

        // 既存データの移行：problem_solvingsからplansテーブルへ
        $existingData = DB::table('problem_solvings')
            ->whereNotNull('action_plan')
            ->orWhereNotNull('reflection')
            ->get(['id', 'action_plan', 'reflection', 'created_at', 'updated_at']);

        foreach ($existingData as $row) {
            // action_planかreflectionが入力されている場合のみ移行
            if ($row->action_plan !== null || $row->reflection !== null) {
                DB::table('problem_solving_plans')->insert([
                    'problem_solving_id' => $row->id,
                    'plan_number' => 1,
                    'action_plan' => $row->action_plan,
                    'reflection' => $row->reflection,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_solving_plans');
    }
};

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
        Schema::table('problem_solvings', function (Blueprint $table) {
            $table->dropColumn(['action_plan', 'reflection']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problem_solvings', function (Blueprint $table) {
            $table->text('action_plan')->nullable()->after('improved_image');
            $table->text('reflection')->nullable()->after('action_plan');
        });
    }
};

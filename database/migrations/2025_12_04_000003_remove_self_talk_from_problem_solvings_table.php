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
            $table->dropColumn('self_talk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problem_solvings', function (Blueprint $table) {
            $table->text('self_talk')->nullable()->comment('自分への声かけ（Step 2）');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('problem_solving_plans', function (Blueprint $table) {
            $table->unsignedTinyInteger('improvement_level')->nullable()->after('reflection');
        });
    }

    public function down(): void
    {
        Schema::table('problem_solving_plans', function (Blueprint $table) {
            $table->dropColumn('improvement_level');
        });
    }
};

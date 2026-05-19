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
        if (! Schema::hasColumn('stressor_and_responses', 'stimulated_schemas')) {
            return;
        }

        Schema::table('stressor_and_responses', function (Blueprint $table) {
            $table->dropColumn('stimulated_schemas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('stressor_and_responses', 'stimulated_schemas')) {
            return;
        }

        Schema::table('stressor_and_responses', function (Blueprint $table) {
            $table->json('stimulated_schemas')->nullable()->after('behavior');
        });
    }
};

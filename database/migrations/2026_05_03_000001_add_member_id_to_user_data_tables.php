<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MULTI_RECORD_TABLES = [
        'copings',
        'columns',
        'writing_disclosures',
        'problem_solvings',
        'simple_notepads',
        'stressor_and_responses',
        'support_networks',
        'schema_mode_monitorings',
        'dialogue_works',
        'chronologies',
    ];

    private const SINGLE_RECORD_TABLES = [
        'early_maladaptive_schemas',
        'safe_places',
        'mode_maps',
        'happy_schema_action_plans',
        'healthy_adult_mode_images',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::MULTI_RECORD_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('member_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('members')
                    ->cascadeOnDelete();

                $table->index('member_id');
            });
        }

        foreach (self::SINGLE_RECORD_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('member_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('members')
                    ->cascadeOnDelete();

                $table->unique('member_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::MULTI_RECORD_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign(['member_id']);

                if (Schema::hasIndex($tableName, ['member_id'])) {
                    $table->dropIndex(['member_id']);
                }

                $table->dropColumn('member_id');
            });
        }

        foreach (self::SINGLE_RECORD_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign(['member_id']);

                if (Schema::hasIndex($tableName, ['member_id'], 'unique')) {
                    $table->dropUnique(['member_id']);
                }

                $table->dropColumn('member_id');
            });
        }
    }
};

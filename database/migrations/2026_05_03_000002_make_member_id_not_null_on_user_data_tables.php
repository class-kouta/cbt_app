<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        foreach ($this->targetTables() as $tableName) {
            $this->ensureNoNullMemberIds($tableName);
            $this->changeMemberIdNullable($tableName, false);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->targetTables() as $tableName) {
            $this->changeMemberIdNullable($tableName, true);
        }
    }

    /**
     * @return array<int, string>
     */
    private function targetTables(): array
    {
        return [
            ...self::MULTI_RECORD_TABLES,
            ...self::SINGLE_RECORD_TABLES,
        ];
    }

    private function ensureNoNullMemberIds(string $tableName): void
    {
        $nullCount = DB::table($tableName)
            ->whereNull('member_id')
            ->count();

        if ($nullCount > 0) {
            throw new RuntimeException(sprintf(
                'Cannot make %s.member_id NOT NULL because %d rows still have NULL member_id. Run AssignMemberIdSeeder first.',
                $tableName,
                $nullCount
            ));
        }
    }

    private function changeMemberIdNullable(string $tableName, bool $nullable): void
    {
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropForeign(['member_id']);
        });

        Schema::table($tableName, function (Blueprint $table) use ($nullable) {
            $table->unsignedBigInteger('member_id')
                ->nullable($nullable)
                ->change();
        });

        Schema::table($tableName, function (Blueprint $table) {
            $table->foreign('member_id')
                ->references('id')
                ->on('members')
                ->cascadeOnDelete();
        });
    }
};

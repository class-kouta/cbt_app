<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignMemberIdSeeder extends Seeder
{
    private const TARGET_MEMBER_ID = 4;

    private const MULTI_RECORD_TABLES = [
        'copings',
        'columns',
        'writing_disclosures',
        'problem_solvings',
        'simple_notepads',
        'stressor_and_responses',
        'support_networks',
        'dialogue_works',
        'chronologies',
    ];

    private const SINGLE_RECORD_TABLES = [
        'early_maladaptive_schemas',
        'mode_maps',
        'healthy_adult_mode_images',
    ];

    /**
     * Assign existing user data to the target member before member_id is made NOT NULL.
     */
    public function run(): void
    {
        DB::transaction(function () {
            foreach (self::MULTI_RECORD_TABLES as $tableName) {
                DB::table($tableName)
                    ->whereNull('member_id')
                    ->update(['member_id' => self::TARGET_MEMBER_ID]);
            }

            foreach (self::SINGLE_RECORD_TABLES as $tableName) {
                $latestId = DB::table($tableName)
                    ->orderByDesc('id')
                    ->value('id');

                if ($latestId === null) {
                    continue;
                }

                DB::table($tableName)
                    ->where('id', '<>', $latestId)
                    ->delete();

                DB::table($tableName)
                    ->where('id', $latestId)
                    ->update(['member_id' => self::TARGET_MEMBER_ID]);
            }
        });
    }
}

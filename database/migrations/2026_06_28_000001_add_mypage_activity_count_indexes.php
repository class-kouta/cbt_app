<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイページの「今日やったこと」集計クエリ向けインデックス。
     * member_id + created_at の範囲検索でインデックスを効かせる。
     */
    private const MEMBER_CREATED_AT_INDEX_TABLES = [
        'columns',
        'writing_disclosures',
        'stressor_and_responses',
        'problem_solvings',
        'support_networks',
        'copings',
        'chronologies',
        'dialogue_works',
        'simple_notepads',
        'exposures',
    ];

    /**
     * member_id を持たない子テーブル（親リレーション経由で絞り込み + created_at 範囲検索）
     */
    private const CREATED_AT_INDEX_TABLES = [
        'problem_solving_plans',
        'exposure_sessions',
    ];

    /**
     * @param  array<int, string>|string  $columns
     */
    private function hasIndex(string $tableName, array|string $columns): bool
    {
        $columns = (array) $columns;

        foreach (Schema::getIndexes($tableName) as $index) {
            if ($index['columns'] === $columns) {
                return true;
            }
        }

        return false;
    }

    public function up(): void
    {
        foreach (self::MEMBER_CREATED_AT_INDEX_TABLES as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            if ($this->hasIndex($tableName, ['member_id', 'created_at'])) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->index(['member_id', 'created_at']);
            });
        }

        foreach (self::CREATED_AT_INDEX_TABLES as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            if ($this->hasIndex($tableName, 'created_at')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        foreach (self::MEMBER_CREATED_AT_INDEX_TABLES as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            if (! $this->hasIndex($tableName, ['member_id', 'created_at'])) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['member_id', 'created_at']);
            });
        }

        foreach (self::CREATED_AT_INDEX_TABLES as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            if (! $this->hasIndex($tableName, 'created_at')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['created_at']);
            });
        }
    }
};

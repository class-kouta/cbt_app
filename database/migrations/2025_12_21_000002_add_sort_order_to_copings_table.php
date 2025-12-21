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
        Schema::table('copings', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('point');
            $table->index('sort_order');
        });

        // 既存レコードにsort_orderを設定（作成日時順）
        DB::statement('
            UPDATE copings
            SET sort_order = subquery.row_num
            FROM (
                SELECT id, ROW_NUMBER() OVER (ORDER BY created_at ASC) as row_num
                FROM copings
            ) AS subquery
            WHERE copings.id = subquery.id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('copings', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};

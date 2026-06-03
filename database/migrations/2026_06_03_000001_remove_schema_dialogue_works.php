<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('dialogue_works')
            ->whereIn('type', ['healthy', 'schema'])
            ->delete();
    }

    public function down(): void
    {
        // 削除されたデータは復元不可
    }
};

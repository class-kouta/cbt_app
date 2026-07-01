<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 筆記開示機能削除に伴い writing_disclosures テーブルを削除する。
     */
    public function up(): void
    {
        Schema::dropIfExists('writing_disclosures');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 機能削除のため復元しない
    }
};

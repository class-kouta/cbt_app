<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dialogue_works', function (Blueprint $table) {
            $table->string('type', 20)->default('schema')->after('id')->comment('対話ワーク種別: schema or mode');
            $table->string('mode_category', 50)->nullable()->after('type')->comment('モードカテゴリ');
            $table->string('mode_name', 100)->nullable()->after('mode_category')->comment('モード名');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('dialogue_works', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'mode_category', 'mode_name']);
        });
    }
};

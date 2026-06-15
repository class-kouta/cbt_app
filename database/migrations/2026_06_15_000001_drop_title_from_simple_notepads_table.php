<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simple_notepads', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }

    public function down(): void
    {
        Schema::table('simple_notepads', function (Blueprint $table) {
            $table->string('title', 255)->default('')->after('id')->comment('メモタイトル');
        });
    }
};

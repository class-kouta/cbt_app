<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * シンプルメモ帳テーブル（思考の外在化用）
     */
    public function up(): void
    {
        Schema::create('simple_notepads', function (Blueprint $table) {
            $table->id();
            $table->text('content')->comment('メモ内容');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simple_notepads');
    }
};

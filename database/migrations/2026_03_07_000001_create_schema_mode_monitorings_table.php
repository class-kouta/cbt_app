<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * スキーマとモードのセルフモニタリングテーブル
     */
    public function up(): void
    {
        Schema::create('schema_mode_monitorings', function (Blueprint $table) {
            $table->id();
            $table->text('content')->comment('モニタリング内容');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schema_mode_monitorings');
    }
};

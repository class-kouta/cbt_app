<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('columns', function (Blueprint $table) {
            // situation以外のカラムをnullableに変更
            $table->text('mood')->nullable()->change();
            $table->text('automatic_thought')->nullable()->change();
            $table->text('evidence')->nullable()->change();
            $table->text('counter_evidence')->nullable()->change();
            $table->text('adaptive_thought')->nullable()->change();
            $table->text('current_mood')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('columns', function (Blueprint $table) {
            // 元に戻す
            $table->text('mood')->nullable(false)->change();
            $table->text('automatic_thought')->nullable(false)->change();
            $table->text('evidence')->nullable(false)->change();
            $table->text('counter_evidence')->nullable(false)->change();
            $table->text('adaptive_thought')->nullable(false)->change();
            $table->text('current_mood')->nullable(false)->change();
        });
    }
};

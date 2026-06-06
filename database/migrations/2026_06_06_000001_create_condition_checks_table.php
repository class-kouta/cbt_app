<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * コンディションチェック（現在の心身状態の記録）
     */
    public function up(): void
    {
        Schema::create('condition_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('mood')->comment('気分 1-5');
            $table->unsignedTinyInteger('fatigue')->comment('疲労感 1-5');
            $table->unsignedTinyInteger('anxiety')->comment('不安 1-5');
            $table->unsignedTinyInteger('sleepiness')->comment('眠気 1-5');
            $table->unsignedTinyInteger('physical_condition')->comment('体の調子 1-5');
            $table->text('memo')->nullable()->comment('メモ');
            $table->timestamps();

            $table->index('member_id');
            $table->index(['member_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condition_checks');
    }
};

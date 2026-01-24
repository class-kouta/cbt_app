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
        Schema::create('anxiety_diaries', function (Blueprint $table) {
            $table->id();
            $table->text('situation'); // 状況（必須）
            $table->text('anxiety_thought')->nullable(); // どんな不安が思い浮かんだか
            $table->text('actual_outcome')->nullable(); // 実際にどうなったか
            $table->unsignedBigInteger('stressor_and_response_id')->nullable(); // 転記元のストレッサーとストレス反応ID
            $table->timestamps();

            // 外部キー制約
            $table->foreign('stressor_and_response_id')
                ->references('id')
                ->on('stressor_and_responses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anxiety_diaries');
    }
};

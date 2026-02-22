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
        Schema::dropIfExists('anxiety_diaries');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('anxiety_diaries', function (Blueprint $table) {
            $table->id();
            $table->text('situation');
            $table->text('anxiety_thought')->nullable();
            $table->text('actual_outcome')->nullable();
            $table->unsignedBigInteger('stressor_and_response_id')->nullable();
            $table->timestamps();

            $table->foreign('stressor_and_response_id')
                ->references('id')
                ->on('stressor_and_responses')
                ->onDelete('set null');
        });
    }
};

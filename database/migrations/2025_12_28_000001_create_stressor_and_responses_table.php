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
        Schema::create('stressor_and_responses', function (Blueprint $table) {
            $table->id();
            $table->text('stressor');
            $table->text('cognition')->nullable();
            $table->text('mood')->nullable();
            $table->text('body_reaction')->nullable();
            $table->text('behavior')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stressor_and_responses');
    }
};

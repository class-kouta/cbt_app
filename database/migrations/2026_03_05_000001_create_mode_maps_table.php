<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mode_maps', function (Blueprint $table) {
            $table->id();
            $table->text('wounded_child_mode')->nullable();
            $table->text('hurtful_adult_mode')->nullable();
            $table->text('unacceptable_coping_mode')->nullable();
            $table->text('healthy_happy_child_mode')->nullable();
            $table->text('healthy_adult_mode')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mode_maps');
    }
};

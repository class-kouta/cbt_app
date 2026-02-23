<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chronologies', function (Blueprint $table) {
            $table->id();
            $table->string('when_period', 200);
            $table->text('environment_event')->nullable();
            $table->text('experience_feeling')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chronologies');
    }
};

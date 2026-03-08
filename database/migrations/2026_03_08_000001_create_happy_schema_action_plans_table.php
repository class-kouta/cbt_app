<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('happy_schema_action_plans', function (Blueprint $table) {
            $table->id();
            $table->text('happy_schema')->nullable();
            $table->text('action_plan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('happy_schema_action_plans');
    }
};

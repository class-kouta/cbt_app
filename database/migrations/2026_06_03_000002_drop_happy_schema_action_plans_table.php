<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('happy_schema_action_plans');
    }

    public function down(): void
    {
        Schema::create('happy_schema_action_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();
            $table->unique('member_id');
            $table->text('happy_schema')->nullable();
            $table->text('action_plan')->nullable();
            $table->timestamps();
        });
    }
};

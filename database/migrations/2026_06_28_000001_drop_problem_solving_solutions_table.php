<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('problem_solving_solutions');
    }

    public function down(): void
    {
        Schema::create('problem_solving_solutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_solving_id')->constrained('problem_solvings')->cascadeOnDelete();
            $table->text('content');
            $table->unsignedTinyInteger('effectiveness')->nullable();
            $table->unsignedTinyInteger('feasibility')->nullable();
            $table->integer('sort_order');
            $table->timestamps();
        });
    }
};

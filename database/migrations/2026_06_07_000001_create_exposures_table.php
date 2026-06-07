<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exposures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->text('avoidance_target');
            $table->string('exposure_type', 32)->nullable();
            $table->text('self_talk')->nullable();
            $table->text('overall_reflection')->nullable();
            $table->text('next_goal')->nullable();
            $table->timestamps();

            $table->index('member_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exposures');
    }
};

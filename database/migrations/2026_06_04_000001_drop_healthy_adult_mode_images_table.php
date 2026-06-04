<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('healthy_adult_mode_images');
    }

    public function down(): void
    {
        Schema::create('healthy_adult_mode_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();
            $table->unique('member_id');
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('safe_places');
    }

    public function down(): void
    {
        Schema::create('safe_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();
            $table->unique('member_id');
            $table->text('safe_image')->nullable();
            $table->text('safe_something')->nullable();
            $table->timestamps();
        });
    }
};

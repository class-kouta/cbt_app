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
        Schema::create('simple_notepad_tag', function (Blueprint $table) {
            $table->foreignId('simple_notepad_id')->constrained()->cascadeOnDelete();
            $table->foreignId('simple_notepad_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['simple_notepad_id', 'simple_notepad_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simple_notepad_tag');
    }
};

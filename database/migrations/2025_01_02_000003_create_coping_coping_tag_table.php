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
        Schema::create('coping_coping_tag', function (Blueprint $table) {
            $table->foreignId('coping_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coping_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['coping_id', 'coping_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coping_coping_tag');
    }
};

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
        Schema::dropIfExists('schema_mode_monitorings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('schema_mode_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->text('content')->comment('モニタリング内容');
            $table->timestamps();
            $table->index('member_id');
        });
    }
};

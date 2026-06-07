<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exposure_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exposure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hierarchy_item_id')->nullable()->constrained('exposure_hierarchy_items')->nullOnDelete();
            $table->unsignedInteger('session_number')->default(1);
            $table->text('action_plan')->nullable();
            $table->unsignedTinyInteger('suds_before')->nullable();
            $table->unsignedTinyInteger('suds_peak')->nullable();
            $table->unsignedTinyInteger('suds_after')->nullable();
            $table->timestamp('performed_at')->nullable();
            $table->text('reflection')->nullable();
            $table->timestamps();

            $table->unique(['exposure_id', 'session_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exposure_sessions');
    }
};

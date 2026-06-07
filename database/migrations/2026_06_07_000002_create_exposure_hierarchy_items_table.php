<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exposure_hierarchy_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exposure_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->unsignedTinyInteger('expected_suds')->nullable();
            $table->integer('sort_order');
            $table->timestamps();

            $table->index(['exposure_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exposure_hierarchy_items');
    }
};

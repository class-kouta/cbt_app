<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('difficulties', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10)->comment('表示名: 小 / 中 / 大');
            $table->tinyInteger('points')->comment('重み付け: 1 / 2 / 3');
            $table->char('color', 7)->nullable()->comment('色: #FFAA00 など');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('difficulties');
    }
};

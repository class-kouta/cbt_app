<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 筆記開示（反芻思考の外在化）テーブル
     */
    public function up(): void
    {
        Schema::create('writing_disclosures', function (Blueprint $table) {
            $table->id();
            $table->text('content')->comment('メモ内容');
            $table->text('note')->nullable()->comment('備考欄');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_disclosures');
    }
};

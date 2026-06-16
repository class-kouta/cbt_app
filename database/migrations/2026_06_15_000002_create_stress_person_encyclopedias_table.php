<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ストレス人物図鑑
     */
    public function up(): void
    {
        Schema::create('stress_person_encyclopedias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('名前');
            $table->text('relationship')->nullable()->comment('関係性');
            $table->text('difficult_traits')->nullable()->comment('苦手な特徴');
            $table->text('my_reaction')->nullable()->comment('自分の反応');
            $table->text('coping_strategy')->nullable()->comment('対応方針');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();

            $table->index('member_id');
            $table->index(['member_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stress_person_encyclopedias');
    }
};

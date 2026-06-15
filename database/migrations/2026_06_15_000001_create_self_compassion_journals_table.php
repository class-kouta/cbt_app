<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * セルフコンパッション日記
     */
    public function up(): void
    {
        Schema::create('self_compassion_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->text('difficult_experience')->comment('しんどかったこと');
            $table->text('effort_made')->comment('それでも頑張ったこと');
            $table->text('friend_voice')->comment('友人だったら自分にどんな声をかけるか');
            $table->text('word_to_self')->comment('自分への一言');
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
        Schema::dropIfExists('self_compassion_journals');
    }
};

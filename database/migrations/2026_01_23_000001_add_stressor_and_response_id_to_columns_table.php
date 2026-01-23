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
        Schema::table('columns', function (Blueprint $table) {
            // 転記元のストレッサーとストレス反応ID（nullable：転記しない場合はnull）
            $table->unsignedBigInteger('stressor_and_response_id')->nullable()->after('notes');
            
            // 外部キー制約（ストレッサーが削除されてもコラムは残す）
            $table->foreign('stressor_and_response_id')
                ->references('id')
                ->on('stressor_and_responses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('columns', function (Blueprint $table) {
            $table->dropForeign(['stressor_and_response_id']);
            $table->dropColumn('stressor_and_response_id');
        });
    }
};

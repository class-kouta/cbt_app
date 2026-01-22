<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tags = [
            '人間関係',
            '勉強',
            'キャリア',
            '学校',
            '恋愛',
            '夫婦',
            '家庭',
            '育児',
            '健康',
            'お金',
        ];

        $now = now();

        foreach ($tags as $tag) {
            DB::table('tags')->insert([
                'name' => $tag,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tags')->whereIn('name', [
            '人間関係',
            '勉強',
            'キャリア',
            '学校',
            '恋愛',
            '夫婦',
            '家庭',
            '育児',
            '健康',
            'お金',
        ])->delete();
    }
};

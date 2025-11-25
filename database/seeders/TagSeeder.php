<?php

namespace Database\Seeders;

use App\Infrastructure\Database\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * サンプルタグをシードする
     */
    public function run(): void
    {
        $tags = [
            '個人開発',
            '家事',
            '育児',
            '勉強',
            '仕事',
            '健康',
            '趣味',
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }

        // PostgreSQLのシーケンスをリセット（次のIDが正しく採番されるように）
        if (DB::connection()->getDriverName() === 'pgsql') {
            $maxId = Tag::max('id') ?? 0;
            DB::statement("SELECT setval('tags_id_seq', ?)", [$maxId]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Infrastructure\Database\Models\CopingTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CopingTagSeeder extends Seeder
{
    /**
     * コーピング用デフォルトタグをシードする
     */
    public function run(): void
    {
        $tags = [
            // リラックス系
            'リラックス',
            '睡眠・休息',
            '入浴',
            // 体を動かす系
            '運動',
            '散歩',
            // 趣味・娯楽系
            '趣味',
            '音楽',
            '読書',
            '動画・映画',
            'ゲーム',
            // 人との交流系
            '人と話す',
            'SNS',
            // 食べ物・飲み物系
            '食べ物',
            '飲み物',
            // 外出系
            '外出',
            '買い物',
            // その他
            '気分転換',
            '掃除・片付け',
            '深呼吸',
            'ご褒美',
        ];

        foreach ($tags as $tagName) {
            CopingTag::firstOrCreate(['name' => $tagName]);
        }

        // PostgreSQLのシーケンスをリセット（次のIDが正しく採番されるように）
        if (DB::connection()->getDriverName() === 'pgsql') {
            $maxId = CopingTag::max('id') ?? 0;
            DB::statement("SELECT setval('coping_tags_id_seq', ?)", [$maxId]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Infrastructure\Database\Models\Difficulty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DifficultySeeder extends Seeder
{
    /**
     * 難易度の初期データをシードする
     */
    public function run(): void
    {
        $difficulties = [
            [
                'id' => 1,
                'name' => '小',
                'points' => 1,
                'color' => '#4CAF50', // グリーン
            ],
            [
                'id' => 2,
                'name' => '中',
                'points' => 2,
                'color' => '#FF9800', // オレンジ
            ],
            [
                'id' => 3,
                'name' => '大',
                'points' => 3,
                'color' => '#F44336', // レッド
            ],
        ];

        foreach ($difficulties as $difficulty) {
            Difficulty::updateOrCreate(
                ['id' => $difficulty['id']],
                $difficulty
            );
        }

        // PostgreSQLのシーケンスをリセット（次のIDが正しく採番されるように）
        if (DB::connection()->getDriverName() === 'pgsql') {
            $maxId = Difficulty::max('id') ?? 0;
            DB::statement("SELECT setval('difficulties_id_seq', ?)", [$maxId]);
        }
    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 難易度とタグの初期データを作成
        $this->call([
            TagSeeder::class,
            CopingTagSeeder::class,
            TestMembersSeeder::class,
        ]);
    }
}

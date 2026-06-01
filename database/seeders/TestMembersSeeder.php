<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestMembersSeeder extends Seeder
{
    /**
     * 検証環境向けのテスト会員を作成する。
     *
     * SEED_TEST_MEMBERS=true のときのみ実行（Review Apps 等）。
     */
    public function run(): void
    {
        if (! config('app.seed_test_members', false)) {
            return;
        }

        $now = now();
        $password = Hash::make('testtesttest');

        DB::table('members')->upsert(
            [
                [
                    'name' => '検証テストユーザー1',
                    'email' => 'ff03csm26test1@example.com',
                    'password' => $password,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => '検証テストユーザー2',
                    'email' => 'ff03csm26test2@example.com',
                    'password' => $password,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['email'],
            ['name', 'password', 'email_verified_at', 'updated_at'],
        );
    }
}

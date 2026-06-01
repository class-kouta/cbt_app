<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * 検証環境向けのテスト会員を作成する。
     *
     * ログインにはメール認証済み（email_verified_at）が必要なため、検証済みとして登録する。
     */
    public function up(): void
    {
        if (! config('app.seed_test_members', false)) {
            return;
        }

        $now = now();
        $password = Hash::make('testtesttest');

        $members = [
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
        ];

        DB::table('members')->upsert(
            $members,
            ['email'],
            ['name', 'password', 'email_verified_at', 'updated_at'],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! config('app.seed_test_members', false)) {
            return;
        }

        DB::table('members')->whereIn('email', [
            'ff03csm26test1@example.com',
            'ff03csm26test2@example.com',
        ])->delete();
    }
};

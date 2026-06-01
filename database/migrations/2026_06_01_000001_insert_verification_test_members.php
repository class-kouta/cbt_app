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
        if (! filter_var(env('SEED_TEST_MEMBERS', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        $now = now();
        $password = Hash::make('testtesttest');

        $members = [
            [
                'name' => '検証テストユーザー1',
                'email' => 'ff03csm26test1@example.com',
            ],
            [
                'name' => '検証テストユーザー2',
                'email' => 'ff03csm26test2@example.com',
            ],
        ];

        foreach ($members as $member) {
            $exists = DB::table('members')->where('email', $member['email'])->exists();

            if ($exists) {
                DB::table('members')->where('email', $member['email'])->update([
                    'name' => $member['name'],
                    'password' => $password,
                    'email_verified_at' => $now,
                    'updated_at' => $now,
                ]);

                continue;
            }

            DB::table('members')->insert([
                'name' => $member['name'],
                'email' => $member['email'],
                'password' => $password,
                'email_verified_at' => $now,
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
        if (! filter_var(env('SEED_TEST_MEMBERS', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        DB::table('members')->whereIn('email', [
            'ff03csm26test1@example.com',
            'ff03csm26test2@example.com',
        ])->delete();
    }
};

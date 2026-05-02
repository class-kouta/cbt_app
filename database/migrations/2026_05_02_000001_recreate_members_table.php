<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `0001_01_01_000000_create_users_table.php` を `members` を作る形に直接書き換えてしまったため、
     * 既にマイグレーション済みの環境（本番）には `users` テーブルしか存在しない。
     * 既存データは保持不要なので、`users` を破棄して `members` を作り直す。
     *
     * 新規環境（既に `members` がある場合）でも冪等に動くよう、`dropIfExists` してから作り直す。
     */
    public function up(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('members');

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        if (Schema::hasTable('sessions')) {
            if (Schema::hasColumn('sessions', 'user_id')) {
                Schema::table('sessions', function (Blueprint $table) {
                    $table->dropColumn('user_id');
                });
            }

            if (! Schema::hasColumn('sessions', 'member_id')) {
                Schema::table('sessions', function (Blueprint $table) {
                    $table->foreignId('member_id')->nullable()->after('id')->index();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};

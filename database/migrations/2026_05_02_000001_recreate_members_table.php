<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `users` テーブルを破棄して `members` テーブルを新規作成する。
     * 併せて `sessions.user_id` を `member_id` にリネームする。
     *
     * 既存環境（本番: 旧 `0001` で `users` 作成済み）／新規環境（修正後 `0001` で `users` 作成）の
     * いずれでも `users` が存在する前提で動作する。
     */
    public function up(): void
    {
        Schema::dropIfExists('users');

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->foreignId('member_id')->nullable()->after('id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('member_id');
            $table->foreignId('user_id')->nullable()->after('id')->index();
        });

        Schema::dropIfExists('members');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
};

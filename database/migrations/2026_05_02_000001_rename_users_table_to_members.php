<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasTable('members')) {
            Schema::rename('users', 'members');
        }

        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id') && !Schema::hasColumn('sessions', 'member_id')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->renameColumn('user_id', 'member_id');
            });
        }

        if (Schema::hasTable('personal_access_tokens')) {
            DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\\Models\\User')
                ->update(['tokenable_type' => 'App\\Models\\Member']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('members') && !Schema::hasTable('users')) {
            Schema::rename('members', 'users');
        }

        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'member_id') && !Schema::hasColumn('sessions', 'user_id')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->renameColumn('member_id', 'user_id');
            });
        }

        if (Schema::hasTable('personal_access_tokens')) {
            DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\\Models\\Member')
                ->update(['tokenable_type' => 'App\\Models\\User']);
        }
    }
};

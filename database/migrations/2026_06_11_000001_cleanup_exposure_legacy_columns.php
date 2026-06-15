<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('exposure_tag');

        Schema::table('exposures', function (Blueprint $table) {
            $table->dropColumn([
                'exposure_type',
                'self_talk',
                'overall_reflection',
                'next_goal',
            ]);
        });

        Schema::table('exposure_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'action_plan',
                'suds_before',
                'suds_peak',
                'performed_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('exposure_sessions', function (Blueprint $table) {
            $table->text('action_plan')->nullable()->after('session_number');
            $table->unsignedTinyInteger('suds_before')->nullable()->after('action_plan');
            $table->unsignedTinyInteger('suds_peak')->nullable()->after('suds_before');
            $table->timestamp('performed_at')->nullable()->after('suds_after');
        });

        Schema::table('exposures', function (Blueprint $table) {
            $table->string('exposure_type', 32)->nullable()->after('avoidance_target');
            $table->text('self_talk')->nullable()->after('exposure_type');
            $table->text('overall_reflection')->nullable()->after('self_talk');
            $table->text('next_goal')->nullable()->after('overall_reflection');
        });

        Schema::create('exposure_tag', function (Blueprint $table) {
            $table->foreignId('exposure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['exposure_id', 'tag_id']);
        });
    }
};

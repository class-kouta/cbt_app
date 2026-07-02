<?php

use App\Enums\SimpleNotepadTagColor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('simple_notepad_tags', function (Blueprint $table) {
            $table->string('color', 20)->default(SimpleNotepadTagColor::Emerald->value)->after('name');
        });

        DB::table('simple_notepad_tags')->orderBy('id')->chunk(100, function ($tags): void {
            foreach ($tags as $tag) {
                DB::table('simple_notepad_tags')
                    ->where('id', $tag->id)
                    ->update([
                        'color' => SimpleNotepadTagColor::defaultForIndex((int) $tag->id)->value,
                    ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simple_notepad_tags', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};

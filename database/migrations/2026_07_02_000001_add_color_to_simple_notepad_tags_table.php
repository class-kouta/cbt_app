<?php

use App\Enums\SimpleNotepadTagColor;
use App\Infrastructure\Database\Models\SimpleNotepadTag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

        SimpleNotepadTag::query()->orderBy('id')->each(function (SimpleNotepadTag $tag): void {
            $tag->update([
                'color' => SimpleNotepadTagColor::defaultForIndex((int) $tag->id)->value,
            ]);
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

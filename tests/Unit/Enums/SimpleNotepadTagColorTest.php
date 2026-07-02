<?php

namespace Tests\Unit\Enums;

use App\Enums\SimpleNotepadTagColor;
use PHPUnit\Framework\TestCase;

class SimpleNotepadTagColorTest extends TestCase
{
    public function test_from_string_returns_emerald_for_unknown_value(): void
    {
        $this->assertSame(SimpleNotepadTagColor::Emerald, SimpleNotepadTagColor::fromString('unknown'));
    }

    public function test_default_for_index_cycles_through_colors(): void
    {
        $colors = SimpleNotepadTagColor::cases();

        $this->assertSame($colors[0], SimpleNotepadTagColor::defaultForIndex(0));
        $this->assertSame($colors[1], SimpleNotepadTagColor::defaultForIndex(1));
        $this->assertSame($colors[0], SimpleNotepadTagColor::defaultForIndex(count($colors)));
    }

    public function test_default_for_index_handles_negative_index(): void
    {
        $colors = SimpleNotepadTagColor::cases();

        $this->assertSame($colors[1], SimpleNotepadTagColor::defaultForIndex(-1));
    }
}

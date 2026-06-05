<?php

namespace Tests\Unit\Support;

use App\Support\LikeSearch;
use PHPUnit\Framework\TestCase;

class LikeSearchTest extends TestCase
{
    public function test_escape_keyword_escapes_like_wildcards(): void
    {
        $this->assertSame('100\\%', LikeSearch::escapeKeyword('100%'));
        $this->assertSame('a\\_b', LikeSearch::escapeKeyword('a_b'));
        $this->assertSame('\\\\test', LikeSearch::escapeKeyword('\\test'));
    }

    public function test_contains_pattern_wraps_escaped_keyword(): void
    {
        $this->assertSame('%100\\%%', LikeSearch::containsPattern('100%'));
        $this->assertSame('%test%', LikeSearch::containsPattern('test'));
    }
}

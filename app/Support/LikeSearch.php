<?php

namespace App\Support;

final class LikeSearch
{
    /**
     * LIKE検索用にワイルドカード文字（\ % _）をエスケープする
     */
    public static function escapeKeyword(string $keyword): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);
    }

    /**
     * 部分一致検索用のLIKEパターンを生成する
     */
    public static function containsPattern(string $keyword): string
    {
        return '%'.self::escapeKeyword($keyword).'%';
    }
}

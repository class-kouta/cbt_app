<?php

namespace App\Infrastructure\Database\Models\Concerns;

use Illuminate\Support\Facades\Auth;

/**
 * ルートモデルバインディング時に、認証済みメンバーの所有リソースのみ解決する。
 */
trait BelongsToAuthenticatedMember
{
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->where('member_id', Auth::id())
            ->first();
    }
}

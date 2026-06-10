<?php

namespace App\Infrastructure\Database\Models\Concerns;

use Illuminate\Support\Facades\Auth;

/**
 * ルートモデルバインディング時に、認証済みメンバーのエクスポージャーに紐づくリソースのみ解決する。
 */
trait BelongsToExposureOfAuthenticatedMember
{
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->whereHas('exposure', fn ($q) => $q->where('member_id', Auth::id()))
            ->first();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * モデルが認証済みメンバーの所有であることを確認する（BOLA/IDOR対策）。
     */
    protected function authorizeMemberOwnership(Model $model, string $memberIdColumn = 'member_id'): void
    {
        if ((int) $model->{$memberIdColumn} !== (int) Auth::id()) {
            abort(404);
        }
    }
}

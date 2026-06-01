<?php

namespace App\Infrastructure\Session;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\DatabaseSessionHandler;

/**
 * members 認証向けに sessions テーブルの外部キー列名を member_id に合わせる。
 */
class MemberDatabaseSessionHandler extends DatabaseSessionHandler
{
    /**
     * @param  array<string, mixed>  $payload
     */
    protected function addUserInformation(&$payload): static
    {
        if ($this->container->bound(Guard::class)) {
            $payload['member_id'] = $this->userId();
        }

        return $this;
    }
}

<?php

namespace App\Application\Exception;

use Exception;

class EmailNotVerifiedException extends Exception
{
    public function __construct(
        public readonly string $email,
    ) {
        parent::__construct('メールアドレスの認証が完了していません。');
    }
}

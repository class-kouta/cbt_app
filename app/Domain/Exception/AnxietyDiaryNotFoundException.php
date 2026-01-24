<?php

namespace App\Domain\Exception;

use RuntimeException;

class AnxietyDiaryNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("AnxietyDiary with ID {$id} not found");
    }
}

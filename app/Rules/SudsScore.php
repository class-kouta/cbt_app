<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SudsScore implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_numeric($value) || (int) $value != $value || $value < 0 || $value > 100 || ((int) $value % 5) !== 0) {
            $fail('不安レベルは0から100まで5刻みで入力してください');
        }
    }
}

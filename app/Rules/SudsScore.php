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

        $intValue = filter_var($value, FILTER_VALIDATE_INT);
        if ($intValue === false || $intValue < 0 || $intValue > 100 || ($intValue % 5) !== 0) {
            $fail('不安レベルは0から100まで5刻みで入力してください');
        }
    }
}

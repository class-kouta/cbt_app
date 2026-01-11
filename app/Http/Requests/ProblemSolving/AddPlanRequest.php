<?php

namespace App\Http\Requests\ProblemSolving;

use Illuminate\Foundation\Http\FormRequest;

class AddPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action_plan' => ['nullable', 'string', 'max:5000'],
            'reflection' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

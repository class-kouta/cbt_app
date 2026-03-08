<?php

namespace App\Http\Requests\HappySchemaActionPlan;

use Illuminate\Foundation\Http\FormRequest;

class CreateHappySchemaActionPlanRequest extends FormRequest
{
    use HappySchemaActionPlanRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->happySchemaActionPlanRules();
    }

    public function messages(): array
    {
        return $this->happySchemaActionPlanMessages();
    }
}

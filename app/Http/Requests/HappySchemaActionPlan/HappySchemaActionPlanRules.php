<?php

namespace App\Http\Requests\HappySchemaActionPlan;

trait HappySchemaActionPlanRules
{
    public function happySchemaActionPlanRules(): array
    {
        return [
            'happy_schema' => ['nullable', 'string', 'max:10000'],
            'action_plan' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function happySchemaActionPlanMessages(): array
    {
        return [
            'happy_schema.max' => 'ハッピースキーマは10000文字以下にしてください',
            'action_plan.max' => 'ハッピースキーマに基づく行動計画は10000文字以下にしてください',
        ];
    }
}

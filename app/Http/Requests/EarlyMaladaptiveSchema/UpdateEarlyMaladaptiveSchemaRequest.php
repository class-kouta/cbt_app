<?php

namespace App\Http\Requests\EarlyMaladaptiveSchema;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEarlyMaladaptiveSchemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 第1領域：切断と拒絶
            'abandonment' => ['nullable', 'integer', 'min:0', 'max:100'],
            'mistrust_abuse' => ['nullable', 'integer', 'min:0', 'max:100'],
            'emotional_deprivation' => ['nullable', 'integer', 'min:0', 'max:100'],
            'defectiveness_shame' => ['nullable', 'integer', 'min:0', 'max:100'],
            'social_isolation' => ['nullable', 'integer', 'min:0', 'max:100'],
            
            // 第2領域：自律性と機能の障害
            'dependence_incompetence' => ['nullable', 'integer', 'min:0', 'max:100'],
            'vulnerability_to_harm' => ['nullable', 'integer', 'min:0', 'max:100'],
            'enmeshment' => ['nullable', 'integer', 'min:0', 'max:100'],
            'failure' => ['nullable', 'integer', 'min:0', 'max:100'],
            
            // 第3領域：制約の欠如
            'entitlement_grandiosity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'insufficient_self_control' => ['nullable', 'integer', 'min:0', 'max:100'],
            
            // 第4領域：他者への志向
            'subjugation' => ['nullable', 'integer', 'min:0', 'max:100'],
            'self_sacrifice' => ['nullable', 'integer', 'min:0', 'max:100'],
            'approval_seeking' => ['nullable', 'integer', 'min:0', 'max:100'],
            
            // 第5領域：過剰警戒と抑制
            'negativity_pessimism' => ['nullable', 'integer', 'min:0', 'max:100'],
            'emotional_inhibition' => ['nullable', 'integer', 'min:0', 'max:100'],
            'unrelenting_standards' => ['nullable', 'integer', 'min:0', 'max:100'],
            'punitiveness' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.integer' => '囚われ度は数値で入力してください',
            '*.min' => '囚われ度は0%以上で入力してください',
            '*.max' => '囚われ度は100%以下で入力してください',
        ];
    }
}

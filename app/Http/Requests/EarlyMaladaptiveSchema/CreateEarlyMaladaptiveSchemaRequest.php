<?php

namespace App\Http\Requests\EarlyMaladaptiveSchema;

use Illuminate\Foundation\Http\FormRequest;

class CreateEarlyMaladaptiveSchemaRequest extends FormRequest
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
            'abandonment_experience' => ['nullable', 'string', 'max:10000'],
            'mistrust_abuse' => ['nullable', 'integer', 'min:0', 'max:100'],
            'mistrust_abuse_experience' => ['nullable', 'string', 'max:10000'],
            'emotional_deprivation' => ['nullable', 'integer', 'min:0', 'max:100'],
            'emotional_deprivation_experience' => ['nullable', 'string', 'max:10000'],
            'defectiveness_shame' => ['nullable', 'integer', 'min:0', 'max:100'],
            'defectiveness_shame_experience' => ['nullable', 'string', 'max:10000'],
            'social_isolation' => ['nullable', 'integer', 'min:0', 'max:100'],
            'social_isolation_experience' => ['nullable', 'string', 'max:10000'],
            
            // 第2領域：自律性と機能の障害
            'dependence_incompetence' => ['nullable', 'integer', 'min:0', 'max:100'],
            'dependence_incompetence_experience' => ['nullable', 'string', 'max:10000'],
            'vulnerability_to_harm' => ['nullable', 'integer', 'min:0', 'max:100'],
            'vulnerability_to_harm_experience' => ['nullable', 'string', 'max:10000'],
            'enmeshment' => ['nullable', 'integer', 'min:0', 'max:100'],
            'enmeshment_experience' => ['nullable', 'string', 'max:10000'],
            'failure' => ['nullable', 'integer', 'min:0', 'max:100'],
            'failure_experience' => ['nullable', 'string', 'max:10000'],
            
            // 第3領域：制約の欠如
            'entitlement_grandiosity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'entitlement_grandiosity_experience' => ['nullable', 'string', 'max:10000'],
            'insufficient_self_control' => ['nullable', 'integer', 'min:0', 'max:100'],
            'insufficient_self_control_experience' => ['nullable', 'string', 'max:10000'],
            
            // 第4領域：他者への志向
            'subjugation' => ['nullable', 'integer', 'min:0', 'max:100'],
            'subjugation_experience' => ['nullable', 'string', 'max:10000'],
            'self_sacrifice' => ['nullable', 'integer', 'min:0', 'max:100'],
            'self_sacrifice_experience' => ['nullable', 'string', 'max:10000'],
            'approval_seeking' => ['nullable', 'integer', 'min:0', 'max:100'],
            'approval_seeking_experience' => ['nullable', 'string', 'max:10000'],
            
            // 第5領域：過剰警戒と抑制
            'negativity_pessimism' => ['nullable', 'integer', 'min:0', 'max:100'],
            'negativity_pessimism_experience' => ['nullable', 'string', 'max:10000'],
            'emotional_inhibition' => ['nullable', 'integer', 'min:0', 'max:100'],
            'emotional_inhibition_experience' => ['nullable', 'string', 'max:10000'],
            'unrelenting_standards' => ['nullable', 'integer', 'min:0', 'max:100'],
            'unrelenting_standards_experience' => ['nullable', 'string', 'max:10000'],
            'punitiveness' => ['nullable', 'integer', 'min:0', 'max:100'],
            'punitiveness_experience' => ['nullable', 'string', 'max:10000'],
            
            // 備考欄
            'notes' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.integer' => '囚われ度は数値で入力してください',
            '*.min' => '囚われ度は0%以上で入力してください',
            '*.max' => '入力値が長すぎます',
            '*_experience.max' => '経験の入力は10000文字以下にしてください',
            'notes.max' => '備考欄は10000文字以下にしてください',
        ];
    }
}

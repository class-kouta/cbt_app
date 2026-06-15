<?php

namespace App\Http\Requests\StressPersonEncyclopedia;

use Illuminate\Foundation\Http\FormRequest;

class CreateStressPersonEncyclopediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'relationship' => ['nullable', 'string', 'max:10000'],
            'difficult_traits' => ['nullable', 'string', 'max:10000'],
            'my_reaction' => ['nullable', 'string', 'max:10000'],
            'coping_strategy' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '名前を入力してください',
            'name.max' => '名前は255文字以内で入力してください',
            'relationship.max' => '関係性は10000文字以内で入力してください',
            'difficult_traits.max' => '苦手な特徴は10000文字以内で入力してください',
            'my_reaction.max' => '自分の反応は10000文字以内で入力してください',
            'coping_strategy.max' => '対応方針は10000文字以内で入力してください',
            'notes.max' => '備考は10000文字以内で入力してください',
        ];
    }
}

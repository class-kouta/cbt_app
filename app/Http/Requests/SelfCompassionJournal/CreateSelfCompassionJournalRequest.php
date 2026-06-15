<?php

namespace App\Http\Requests\SelfCompassionJournal;

use Illuminate\Foundation\Http\FormRequest;

class CreateSelfCompassionJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'difficult_experience' => ['required', 'string', 'max:10000'],
            'effort_made' => ['required', 'string', 'max:10000'],
            'friend_voice' => ['required', 'string', 'max:10000'],
            'word_to_self' => ['required', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'difficult_experience.required' => 'しんどかったことを入力してください',
            'difficult_experience.max' => 'しんどかったことは10000文字以内で入力してください',
            'effort_made.required' => 'それでも頑張ったことを入力してください',
            'effort_made.max' => 'それでも頑張ったことは10000文字以内で入力してください',
            'friend_voice.required' => '友人だったら自分にどんな声をかけるかを入力してください',
            'friend_voice.max' => '友人だったら自分にどんな声をかけるかは10000文字以内で入力してください',
            'word_to_self.required' => '自分への一言を入力してください',
            'word_to_self.max' => '自分への一言は10000文字以内で入力してください',
        ];
    }
}

@php
$fields = [
    [
        'key' => 'difficult_experience',
        'label' => 'しんどかったこと',
        'hint' => '辛かった出来事や気持ちを書いてみましょう',
        'placeholder' => '例：仕事でミスをして落ち込んだ',
    ],
    [
        'key' => 'effort_made',
        'label' => 'それでも頑張ったこと',
        'hint' => '小さな努力でもOKです',
        'placeholder' => '例：翌日も出勤して仕事を続けた',
    ],
    [
        'key' => 'friend_voice',
        'label' => '友人だったら自分にどんな声をかけるか',
        'hint' => '親しい友人のように優しく声をかけてみましょう',
        'placeholder' => '例：ミスは誰にでもあるよ。よく頑張ってるよね',
    ],
    [
        'key' => 'word_to_self',
        'label' => '自分への一言',
        'hint' => '自分に伝えたい言葉を書いてみましょう',
        'placeholder' => '例：今日もよく頑張った。自分を認めてあげよう',
    ],
];
@endphp

@extends('layouts.app')

@section('title', isset($itemId) ? 'セルフコンパッション日記 - 編集' : 'セルフコンパッション日記')
@section('page-title', 'セルフコンパッション日記')

@section('content')
<div x-data="selfCompassionJournalApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a :href="'/self-compassion-journals/' + itemId" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
            ← 詳細に戻る
        </a>
    </div>

    <div x-show="dataLoading && isEditMode" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <div x-show="!isEditMode" class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-4">
        <p class="text-emerald-800 text-sm">
            しんどい気持ちを認めながら、自分に優しく声をかける日記です。4つの項目をすべて記入して保存しましょう。
        </p>
    </div>

    <div x-show="!dataLoading || !isEditMode" class="bg-white rounded-xl shadow-md p-4 sm:p-6">
        <form @submit.prevent="saveJournal()">
            <div class="space-y-5">
                @foreach ($fields as $index => $field)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">{{ $index + 1 }}</span>
                            {{ $field['label'] }}
                            <span class="text-red-500">*</span>
                            <span class="text-gray-400 font-normal ml-1">{{ $field['hint'] }}</span>
                        </label>
                        <textarea
                            x-model="form.{{ $field['key'] }}"
                            rows="6"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                            placeholder="{{ $field['placeholder'] }}"
                            maxlength="10000"
                            required
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="(form.{{ $field['key'] }} || '').length + '/10000'"></div>
                    </div>
                @endforeach

                <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

                <div>
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-3 px-4 rounded-lg font-semibold hover:from-emerald-600 hover:to-teal-600 transition-colors disabled:opacity-50"
                        :disabled="loading || !isFormValid()"
                    >
                        <span x-show="!loading && !isEditMode">保存する</span>
                        <span x-show="!loading && isEditMode">更新する</span>
                        <span x-show="loading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isEditMode ? '更新中...' : '保存中...'"></span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function selfCompassionJournalApp(itemId) {
    const requiredFields = @json(array_column($fields, 'key'));

    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        form: {
            difficult_experience: '',
            effort_made: '',
            friend_voice: '',
            word_to_self: '',
        },
        loading: false,
        dataLoading: false,
        error: '',

        async init() {
            if (this.isEditMode) {
                await this.loadItem();
            }
        },

        isFormValid() {
            return requiredFields.every((field) => (this.form[field] || '').trim() !== '');
        },

        async loadItem() {
            this.dataLoading = true;
            try {
                const res = await apiFetch(`/api/self-compassion-journals/${this.itemId}`);
                if (!res.ok) {
                    throw new Error('データの取得に失敗しました');
                }
                const item = await res.json();
                this.form = {
                    difficult_experience: item.difficult_experience || '',
                    effort_made: item.effort_made || '',
                    friend_voice: item.friend_voice || '',
                    word_to_self: item.word_to_self || '',
                };
            } catch (error) {
                this.error = error.message;
            } finally {
                this.dataLoading = false;
            }
        },

        buildPayload() {
            return {
                difficult_experience: this.form.difficult_experience.trim(),
                effort_made: this.form.effort_made.trim(),
                friend_voice: this.form.friend_voice.trim(),
                word_to_self: this.form.word_to_self.trim(),
            };
        },

        async saveJournal() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = 'すべての項目を入力してください';
                return;
            }

            this.loading = true;
            try {
                const url = this.isEditMode
                    ? `/api/self-compassion-journals/${this.itemId}`
                    : '/api/self-compassion-journals';
                const method = this.isEditMode ? 'PUT' : 'POST';

                const res = await apiFetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.buildPayload()),
                });

                if (!res.ok) {
                    const data = await res.json();
                    const message = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'エラーが発生しました');
                    throw new Error(message);
                }

                window.location.href = this.isEditMode
                    ? `/self-compassion-journals/${this.itemId}`
                    : '/self-compassion-journals/list';
            } catch (e) {
                this.error = e.message;
                this.loading = false;
            }
        },
    };
}
</script>
@endsection

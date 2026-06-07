@php
use App\Enums\ConditionCheckRating;

$ratingFields = ConditionCheckRating::fieldLabels();
$ratingLabels = ConditionCheckRating::labelsByField();
@endphp

@extends('layouts.app')

@section('title', isset($itemId) ? 'コンディションチェック - 編集' : 'コンディションチェック - 新規記録')
@section('page-title', 'コンディションチェック')

@section('content')
<div x-data="conditionCheckFormApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a href="/condition-checks" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
            ← 一覧に戻る
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
            今の気分や体調を5段階で記録しましょう。すべての項目が必須です。
        </p>
    </div>

    <div x-show="!dataLoading || !isEditMode" class="bg-white rounded-xl shadow-md p-4 sm:p-6">
        <form @submit.prevent="saveItem()">
            <div class="space-y-5">
                @foreach ($ratingFields as $field => $label)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            {{ $label }}
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            x-model="form.{{ $field }}"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white"
                        >
                            <option value="">選択してください</option>
                            @foreach ($ratingLabels[$field] as $index => $optionLabel)
                                <option value="{{ $index + 1 }}">{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">メモ</label>
                    <textarea
                        x-model="form.memo"
                        rows="5"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="なんでも自由に書けます..."
                        maxlength="10000"
                    ></textarea>
                    <div class="text-xs text-gray-400 text-right" x-text="(form.memo || '').length + '/10000'"></div>
                </div>

                <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

                <div>
                    <button
                        type="submit"
                        class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50"
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
function conditionCheckFormApp(itemId) {
    const ratingLabels = @json($ratingLabels);
    const fieldLabels = @json($ratingFields);

    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        form: {
            mood: '',
            fatigue: '',
            anxiety: '',
            sleepiness: '',
            physical_condition: '',
            memo: '',
        },
        loading: false,
        dataLoading: false,
        error: '',
        ratingLabels,
        fieldLabels,

        async init() {
            if (this.isEditMode) {
                await this.loadItem();
            }
        },

        isFormValid() {
            return ['mood', 'fatigue', 'anxiety', 'sleepiness', 'physical_condition']
                .every((field) => this.form[field] !== '' && this.form[field] !== null);
        },

        async loadItem() {
            this.dataLoading = true;
            try {
                const res = await apiFetch(`/api/condition-checks/${this.itemId}`);
                if (!res.ok) {
                    throw new Error('データの取得に失敗しました');
                }
                const item = await res.json();
                this.form = {
                    mood: String(item.mood),
                    fatigue: String(item.fatigue),
                    anxiety: String(item.anxiety),
                    sleepiness: String(item.sleepiness),
                    physical_condition: String(item.physical_condition),
                    memo: item.memo || '',
                };
            } catch (error) {
                this.error = error.message;
            } finally {
                this.dataLoading = false;
            }
        },

        buildPayload() {
            return {
                mood: parseInt(this.form.mood, 10),
                fatigue: parseInt(this.form.fatigue, 10),
                anxiety: parseInt(this.form.anxiety, 10),
                sleepiness: parseInt(this.form.sleepiness, 10),
                physical_condition: parseInt(this.form.physical_condition, 10),
                memo: this.form.memo || null,
            };
        },

        async saveItem() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = 'すべての評価項目を選択してください';
                return;
            }

            this.loading = true;
            try {
                const url = this.isEditMode
                    ? `/api/condition-checks/${this.itemId}`
                    : '/api/condition-checks';
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

                window.location.href = '/condition-checks';
            } catch (e) {
                this.error = e.message;
                this.loading = false;
            }
        },
    };
}
</script>
@endsection

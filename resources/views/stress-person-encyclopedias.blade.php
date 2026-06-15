@php
$fields = [
    [
        'key' => 'relationship',
        'label' => '関係性',
        'hint' => 'その人との関係を書いてみましょう',
        'placeholder' => '例：職場の上司、大学の同級生',
    ],
    [
        'key' => 'difficult_traits',
        'label' => '苦手な特徴',
        'hint' => 'ストレスを感じる行動や特徴を書いてみましょう',
        'placeholder' => '例：一方的に話す、批判的な言い方をする',
    ],
    [
        'key' => 'my_reaction',
        'label' => '自分の反応',
        'hint' => 'その人といるときの自分の反応を書いてみましょう',
        'placeholder' => '例：緊張する、言いたいことが言えなくなる',
    ],
    [
        'key' => 'coping_strategy',
        'label' => '対応方針',
        'hint' => 'うまくいった対応や今後試したいことを書いてみましょう',
        'placeholder' => '例：深呼吸してから返答する、距離を保つ',
    ],
    [
        'key' => 'notes',
        'label' => '備考',
        'hint' => 'その他メモがあれば書いてみましょう',
        'placeholder' => '例：月曜の会議のときに特にストレスを感じる',
    ],
];
@endphp

@extends('layouts.app')

@section('title', isset($itemId) ? 'ストレス人物図鑑 - 編集' : 'ストレス人物図鑑')
@section('page-title', 'ストレス人物図鑑')

@section('content')
<div x-data="stressPersonEncyclopediaApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a :href="'/stress-person-encyclopedias/' + itemId" class="text-violet-600 hover:text-violet-800 flex items-center gap-1">
            ← 詳細に戻る
        </a>
    </div>

    <div x-show="dataLoading && isEditMode" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-violet-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <div x-show="!isEditMode" class="bg-violet-50 border border-violet-200 rounded-lg p-4 mb-4">
        <p class="text-violet-800 text-sm">
            ストレスを感じる人物について整理する図鑑です。名前は必須、その他の項目は任意で記入できます。
        </p>
    </div>

    <div x-show="!dataLoading || !isEditMode" class="bg-white rounded-xl shadow-md p-4 sm:p-6">
        <form @submit.prevent="saveEntry()">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white text-xs font-bold mr-1">1</span>
                        名前
                        <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal ml-1">ストレスを感じる人物の名前や呼び方</span>
                    </label>
                    <input
                        type="text"
                        x-model="form.name"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                        placeholder="例：Aさん、上司の田中さん"
                        maxlength="255"
                        required
                    />
                    <div class="text-xs text-gray-400 text-right" x-text="(form.name || '').length + '/255'"></div>
                </div>

                @foreach ($fields as $index => $field)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white text-xs font-bold mr-1">{{ $index + 2 }}</span>
                            {{ $field['label'] }}
                            <span class="text-gray-400 font-normal ml-1">{{ $field['hint'] }}</span>
                        </label>
                        <textarea
                            x-model="form.{{ $field['key'] }}"
                            rows="6"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                            placeholder="{{ $field['placeholder'] }}"
                            maxlength="10000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="(form.{{ $field['key'] }} || '').length + '/10000'"></div>
                    </div>
                @endforeach

                <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

                <div>
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-violet-500 to-purple-500 text-white py-3 px-4 rounded-lg font-semibold hover:from-violet-600 hover:to-purple-600 transition-colors disabled:opacity-50"
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
function stressPersonEncyclopediaApp(itemId) {
    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        form: {
            name: '',
            relationship: '',
            difficult_traits: '',
            my_reaction: '',
            coping_strategy: '',
            notes: '',
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
            return (this.form.name || '').trim() !== '';
        },

        async loadItem() {
            this.dataLoading = true;
            try {
                const res = await apiFetch(`/api/stress-person-encyclopedias/${this.itemId}`);
                if (!res.ok) {
                    throw new Error('データの取得に失敗しました');
                }
                const item = await res.json();
                this.form = {
                    name: item.name || '',
                    relationship: item.relationship || '',
                    difficult_traits: item.difficult_traits || '',
                    my_reaction: item.my_reaction || '',
                    coping_strategy: item.coping_strategy || '',
                    notes: item.notes || '',
                };
            } catch (error) {
                this.error = error.message;
            } finally {
                this.dataLoading = false;
            }
        },

        buildPayload() {
            const payload = {
                name: this.form.name.trim(),
            };

            ['relationship', 'difficult_traits', 'my_reaction', 'coping_strategy', 'notes'].forEach((key) => {
                const value = (this.form[key] || '').trim();
                if (value !== '') {
                    payload[key] = value;
                }
            });

            return payload;
        },

        async saveEntry() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = '名前を入力してください';
                return;
            }

            this.loading = true;
            try {
                const url = this.isEditMode
                    ? `/api/stress-person-encyclopedias/${this.itemId}`
                    : '/api/stress-person-encyclopedias';
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
                    ? `/stress-person-encyclopedias/${this.itemId}`
                    : '/stress-person-encyclopedias/list';
            } catch (e) {
                this.error = e.message;
                this.loading = false;
            }
        },
    };
}
</script>
@endsection

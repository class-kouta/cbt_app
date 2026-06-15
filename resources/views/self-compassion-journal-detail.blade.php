@php
$fieldLabels = [
    ['key' => 'difficult_experience', 'label' => 'しんどかったこと'],
    ['key' => 'effort_made', 'label' => 'それでも頑張ったこと'],
    ['key' => 'friend_voice', 'label' => '友人だったら自分にどんな声をかけるか'],
    ['key' => 'word_to_self', 'label' => '自分への一言'],
];
@endphp

@extends('layouts.app')

@section('title', 'セルフコンパッション日記 - 詳細')
@section('page-title', 'セルフコンパッション日記')

@section('content')
<div x-data="selfCompassionJournalDetailApp()" x-init="init()" x-cloak>
    <div x-show="loading" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 mx-auto text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <div x-show="!loading && journal" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <a href="/self-compassion-journals/list" class="text-teal-600 hover:text-teal-800 flex items-center gap-1 transition-colors">
                ←
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500" x-text="formatDate(journal?.created_at)"></span>
                <a
                    :href="'/self-compassion-journals/' + itemId + '/edit'"
                    class="text-teal-600 hover:text-teal-800 transition-colors p-2 rounded hover:bg-teal-50"
                    title="編集する"
                >
                    <x-icon name="pencil-square" class="w-5 h-5" />
                </a>
                <button
                    @click="deleteJournal()"
                    class="text-red-400 hover:text-red-600 transition-colors p-2 rounded hover:bg-red-50"
                    title="削除"
                >
                    <x-icon name="trash" class="w-5 h-5" />
                </button>
            </div>
        </div>

        <div class="space-y-4">
            @foreach ($fieldLabels as $index => $field)
                <div class="bg-emerald-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-emerald-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-xs">{{ $index + 1 }}</span>
                        {{ $field['label'] }}
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" x-text="journal?.{{ $field['key'] }}"></p>
                </div>
            @endforeach
        </div>
    </div>

    <div x-show="!loading && !journal" class="text-center py-16 bg-white rounded-xl shadow-md">
        <div class="mb-4 flex justify-center text-gray-300">
            <x-icon name="inbox" class="w-16 h-16" />
        </div>
        <p class="text-gray-600 text-lg mb-2">記録が見つかりません</p>
        <a href="/self-compassion-journals/list" class="inline-block mt-4 text-teal-600 hover:text-teal-800">
            ← 一覧に戻る
        </a>
    </div>
</div>

<script>
function selfCompassionJournalDetailApp() {
    return {
        journal: null,
        loading: true,
        itemId: {{ $itemId }},

        async init() {
            await this.loadJournal();
        },

        async loadJournal() {
            try {
                const res = await apiFetch(`/api/self-compassion-journals/${this.itemId}`);
                if (res.ok) {
                    this.journal = await res.json();
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        async deleteJournal() {
            if (!confirm('この記録を削除しますか？')) return;

            try {
                await apiFetch(`/api/self-compassion-journals/${this.itemId}`, {
                    method: 'DELETE',
                });
                window.location.href = '/self-compassion-journals/list';
            } catch (e) {
                console.error(e);
                alert('削除に失敗しました');
            }
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        },
    };
}
</script>
@endsection

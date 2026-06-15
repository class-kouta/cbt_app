@php
$fieldLabels = [
    'difficult_experience' => 'しんどかったこと',
    'effort_made' => 'それでも頑張ったこと',
    'friend_voice' => '友人だったら自分にどんな声をかけるか',
    'word_to_self' => '自分への一言',
];
@endphp

@extends('layouts.app')

@section('title', 'セルフコンパッション日記 - 過去の記録')
@section('page-title', 'セルフコンパッション日記')

@section('content')
<div x-data="selfCompassionJournalListApp()" x-init="init()" x-cloak>
    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2" x-show="!loading && journals.length > 0">
            合計: <span x-text="journals.length" class="font-bold"></span> 件
        </div>

        <template x-for="item in journals" :key="item.id">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <button
                    type="button"
                    @click="toggleItem(item.id)"
                    class="w-full text-left p-4 hover:bg-emerald-50 transition-colors"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-emerald-600 font-medium mb-2" x-text="formatDate(item.created_at)"></div>
                            <p class="text-gray-800 line-clamp-2 break-words" x-text="item.difficult_experience"></p>
                        </div>
                        <svg
                            class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform"
                            :class="expandedId === item.id ? 'rotate-180' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <div x-show="expandedId === item.id" x-collapse class="border-t border-gray-100">
                    <div class="p-4 space-y-4 bg-emerald-50/50">
                        @foreach ($fieldLabels as $key => $label)
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-[10px] font-bold">{{ $loop->iteration }}</span>
                                    <span class="text-xs font-semibold text-emerald-700">{{ $label }}</span>
                                </div>
                                <p class="text-sm text-gray-800 whitespace-pre-wrap break-words pl-7" x-text="item.{{ $key }}"></p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-1"></div>
            </div>
        </template>

        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 mt-2">読み込み中...</p>
        </div>

        <div x-show="!loading && journals.length === 0" class="text-center py-12 text-gray-500">
            <div class="mb-4 flex justify-center text-gray-300">
                <x-icon name="heart" class="w-12 h-12" />
            </div>
            <p>まだ記録がありません</p>
            <a href="/self-compassion-journals" class="text-teal-600 hover:text-teal-800 text-sm mt-4 inline-block">
                セルフコンパッション日記を書いてみましょう →
            </a>
        </div>
    </div>

    <a
        href="/self-compassion-journals"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-emerald-600 hover:to-teal-600 transition-all"
        title="新しい日記を作成"
    >
        ＋
    </a>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
function selfCompassionJournalListApp() {
    return {
        journals: [],
        loading: true,
        expandedId: null,

        async init() {
            await this.loadJournals();
        },

        async loadJournals() {
            this.loading = true;
            try {
                const res = await apiFetch('/api/self-compassion-journals');
                this.journals = await res.json();
            } finally {
                this.loading = false;
            }
        },

        toggleItem(id) {
            this.expandedId = this.expandedId === id ? null : id;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            const h = String(date.getHours()).padStart(2, '0');
            const min = String(date.getMinutes()).padStart(2, '0');
            return `${y}年${m}月${d}日 ${h}時${min}分`;
        },
    };
}
</script>
@endsection

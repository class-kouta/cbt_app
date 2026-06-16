@extends('layouts.app')

@section('title', 'ストレス人物図鑑 - 一覧')
@section('page-title', 'ストレス人物図鑑')

@section('content')
<div x-data="stressPersonEncyclopediaListApp()" x-init="init()" x-cloak>
    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2" x-show="!loading && entries.length > 0">
            合計: <span x-text="entries.length" class="font-bold"></span> 件
        </div>

        <template x-for="item in entries" :key="item.id">
            <a
                :href="'/stress-person-encyclopedias/' + item.id"
                class="block bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg hover:border-violet-300 transition-all"
            >
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <h3 class="text-lg font-bold text-gray-900 break-words" x-text="item.name"></h3>
                        <span class="text-xs text-violet-600 font-medium whitespace-nowrap" x-text="formatDate(item.created_at)"></span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1" x-show="item.relationship">
                        関係性: <span x-text="item.relationship"></span>
                    </p>
                    <p class="text-gray-800 line-clamp-2 break-words" x-show="item.difficult_traits" x-text="item.difficult_traits"></p>
                </div>
                <div class="bg-gradient-to-r from-violet-500 to-purple-500 h-1"></div>
            </a>
        </template>

        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <svg class="animate-spin h-8 w-8 text-violet-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 mt-2">読み込み中...</p>
        </div>

        <div x-show="!loading && entries.length === 0" class="text-center py-12 text-gray-500">
            <div class="mb-4 flex justify-center text-gray-300">
                <x-icon name="user-group" class="w-12 h-12" />
            </div>
            <p>まだ記録がありません</p>
            <a href="/stress-person-encyclopedias" class="text-purple-600 hover:text-purple-800 text-sm mt-4 inline-block">
                ストレス人物図鑑を作成してみましょう →
            </a>
        </div>
    </div>

    <a
        href="/stress-person-encyclopedias"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-violet-500 to-purple-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-violet-600 hover:to-purple-600 transition-all"
        title="新しい記録を作成"
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
function stressPersonEncyclopediaListApp() {
    return {
        entries: [],
        loading: true,

        async init() {
            await this.loadEntries();
        },

        async loadEntries() {
            this.loading = true;
            try {
                const res = await apiFetch('/api/stress-person-encyclopedias');
                if (!res.ok) {
                    throw new Error('データの取得に失敗しました');
                }
                this.entries = await res.json();
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
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

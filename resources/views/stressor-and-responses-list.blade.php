@extends('layouts.app')

@section('title', 'ストレッサーとストレス反応一覧')
@section('page-title', 'ストレッサーとストレス反応')

@section('content')
<div x-data="stressorListApp()" x-init="init()" x-cloak>
    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="item in items" :key="item.id">
            <a
                :href="'/stressor-and-responses/' + item.id"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-emerald-300"
            >
                <div class="p-4">
                    <!-- 日付 -->
                    <div class="text-xs text-emerald-600 font-medium mb-2" x-text="formatDate(item.created_at)"></div>
                    <!-- ストレッサー -->
                    <p class="text-gray-800 line-clamp-2 break-words overflow-wrap-anywhere mb-2" x-text="item.stressor"></p>
                    <!-- 未入力項目タグ -->
                    <div class="flex flex-wrap gap-1" x-show="getIncompleteFields(item).length > 0">
                        <span class="inline-block px-2 py-0.5 rounded text-xs bg-gray-200 text-gray-600">未入力</span>
                        <span class="inline-block px-2 py-0.5 rounded text-xs bg-gray-200 text-gray-600" x-text="getIncompleteFields(item).join('、')"></span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-1"></div>
            </a>
        </template>

        <!-- ローディング中 -->
        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <div class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-8 w-8 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600 text-lg">読み込み中...</p>
            </div>
        </div>

        <!-- 空の状態 -->
        <div x-show="!loading && items.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">⚡</p>
            <p class="text-gray-600 text-lg mb-2">まだ記録がありません</p>
            <a href="/stressor-and-responses" class="inline-block mt-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-2 px-6 rounded-lg font-medium hover:from-emerald-600 hover:to-teal-600 transition-all">
                新しく記録する
            </a>
        </div>
    </div>

    <!-- 新規作成ボタン（フローティング） -->
    <a
        href="/stressor-and-responses"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-emerald-600 hover:to-teal-600 transition-all"
        title="新しく記録する"
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
function stressorListApp() {
    return {
        items: [],
        loading: true,

        async init() {
            await this.loadItems();
        },

        async loadItems() {
            this.loading = true;
            const res = await fetch('/api/stressor-and-responses');
            this.items = await res.json();
            this.loading = false;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        getIncompleteFields(item) {
            const fieldNames = {
                cognition: '認知',
                mood: '気分・感情',
                body_reaction: '身体反応',
                behavior: '行動'
            };
            
            const incompleteFields = [];
            for (const [key, label] of Object.entries(fieldNames)) {
                if (!item[key] || item[key].trim() === '') {
                    incompleteFields.push(label);
                }
            }
            return incompleteFields;
        }
    };
}
</script>
@endsection

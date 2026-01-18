@extends('layouts.app')

@section('title', '問題解決法一覧')
@section('page-title', '問題解決法')

@section('content')
<div x-data="problemSolvingListApp()" x-init="init()" x-cloak>
    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="item in items" :key="item.id">
            <a
                :href="'/problem-solvings/' + item.id"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-emerald-300"
            >
                <div class="p-4">
                    <!-- 日付 -->
                    <div class="text-xs text-emerald-500 font-medium mb-2" x-text="formatDate(item.created_at)"></div>
                    <!-- 問題状況 -->
                    <p class="text-gray-800 line-clamp-2 break-words overflow-wrap-anywhere mb-2" x-text="item.problem_situation"></p>
                    <!-- 実行計画ステータス -->
                    <div x-data="{ hasPlan: hasActionPlan(item) }" class="flex items-center gap-1">
                        <span class="text-xs text-gray-500">実行計画 :</span>
                        <span class="inline-block px-2 py-0.5 rounded text-xs" :class="hasPlan ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700'" x-text="hasPlan ? '策定済' : '未策定'"></span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-1"></div>
            </a>
        </template>

        <!-- ローディング中 -->
        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 text-lg mt-2">読み込み中...</p>
        </div>

        <!-- 空の状態 -->
        <div x-show="!loading && items.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">🧩</p>
            <p class="text-gray-600 text-lg mb-2">まだ問題解決の記録がありません</p>
            <a href="/problem-solvings" class="inline-block mt-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-2 px-6 rounded-lg font-medium hover:from-emerald-600 hover:to-teal-600 transition-all">
                問題解決を始める
            </a>
        </div>
    </div>

    <!-- 新規作成ボタン（フローティング） -->
    <a
        href="/problem-solvings"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-emerald-600 hover:to-teal-600 transition-all"
        title="新しい問題解決を作成"
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
function problemSolvingListApp() {
    return {
        items: [],
        loading: true,

        async init() {
            await this.loadItems();
        },

        async loadItems() {
            this.loading = true;
            const res = await fetch('/api/problem-solvings');
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

        hasActionPlan(item) {
            return item.action_plan && item.action_plan.trim() !== '';
        }
    };
}
</script>
@endsection

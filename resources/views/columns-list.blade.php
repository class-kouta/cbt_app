@extends('layouts.app')

@section('title', 'コラム一覧')

@section('content')
<div x-data="columnListApp()" x-init="init()" x-cloak>
    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="column in columns" :key="column.id">
            <a
                :href="'/columns/' + column.id"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-indigo-300"
            >
                <div class="p-4">
                    <!-- 日付 -->
                    <div class="text-xs text-indigo-500 font-medium mb-2" x-text="formatDate(column.created_at)"></div>
                    <!-- 状況 -->
                    <p class="text-gray-800 line-clamp-2" x-text="column.situation"></p>
                </div>
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-1"></div>
            </a>
        </template>

        <!-- 空の状態 -->
        <div x-show="columns.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">📝</p>
            <p class="text-gray-600 text-lg mb-2">まだコラムがありません</p>
            <a href="/columns" class="inline-block mt-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-2 px-6 rounded-lg font-medium hover:from-indigo-600 hover:to-purple-600 transition-all">
                コラムを作成する
            </a>
        </div>
    </div>

    <!-- 新規作成ボタン（フローティング） -->
    <a
        href="/columns"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-indigo-600 hover:to-purple-600 transition-all"
        title="新しいコラムを作成"
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
function columnListApp() {
    return {
        columns: [],

        async init() {
            await this.loadColumns();
        },

        async loadColumns() {
            const res = await fetch('/api/columns');
            this.columns = await res.json();
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    };
}
</script>
@endsection

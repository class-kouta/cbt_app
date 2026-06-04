@extends('layouts.app')

@section('title', '適応的思考一覧')
@section('page-title', '適応的思考')

@section('content')
<div x-data="adaptiveThoughtListApp()" x-init="init()" x-cloak>
    <!-- 説明 -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-4 border border-gray-100">
        <p class="text-gray-600 text-sm">
            コラム法で記録した「適応的思考」を一覧で確認できます。<br>
            バランスの取れた考え方を振り返り、日々の思考に活かしましょう。
        </p>
    </div>

    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="column in columns" :key="column.id">
            <a
                :href="'/columns/' + column.id"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-emerald-300"
            >
                <div class="p-4">
                    <!-- 日付 -->
                    <div class="text-xs text-emerald-600 font-medium mb-2" x-text="formatDate(column.created_at)"></div>
                    <!-- 状況（ラベル付き） -->
                    <div class="mb-3">
                        <span class="inline-block px-2 py-0.5 rounded text-xs bg-gray-200 text-gray-600 mb-1">状況</span>
                        <p class="text-gray-500 text-sm break-words overflow-wrap-anywhere" x-text="column.situation"></p>
                    </div>
                    <!-- 適応的思考（全文表示） -->
                    <div>
                        <span class="inline-block px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700 mb-1">適応的思考</span>
                        <p class="text-gray-800 break-words overflow-wrap-anywhere whitespace-pre-wrap" x-text="column.adaptive_thought"></p>
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
        <div x-show="!loading && columns.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <div class="mb-4 flex justify-center text-gray-300"><x-icon name="chat-bubble-bottom-center-text" class="w-16 h-16" /></div>
            <p class="text-gray-600 text-lg mb-2">適応的思考がまだありません</p>
            <p class="text-gray-500 text-sm mb-4">コラム法で「適応的思考」を記録すると、ここに表示されます</p>
            <a href="/columns" class="inline-block mt-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-2 px-6 rounded-lg font-medium hover:from-emerald-600 hover:to-teal-600 transition-all">
                コラムを作成する
            </a>
        </div>
    </div>

    <!-- 新規作成ボタン（フローティング） -->
    <a
        href="/columns"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-emerald-600 hover:to-teal-600 transition-all"
        title="新しいコラムを作成"
    >
        ＋
    </a>
</div>

<script>
function adaptiveThoughtListApp() {
    return {
        columns: [],
        loading: true,

        async init() {
            await this.loadColumns();
        },

        async loadColumns() {
            this.loading = true;
            const res = await apiFetch('/api/columns/adaptive-thoughts');
            this.columns = await res.json();
            this.loading = false;
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

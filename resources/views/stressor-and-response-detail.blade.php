@extends('layouts.app')

@section('title', 'ストレッサーとストレス反応詳細')
@section('page-title', 'ストレッサーとストレス反応')

@section('content')
<div x-data="stressorDetailApp()" x-init="init()" x-cloak>
    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 mx-auto text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- コンテンツ -->
    <div x-show="!loading && item" class="space-y-4">
        <!-- ヘッダー -->
        <div class="flex items-center justify-between mb-4">
            <a href="/stressor-and-responses/list" class="text-rose-600 hover:text-rose-800 flex items-center gap-1 transition-colors">
                ←
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500" x-text="formatDate(item?.created_at)"></span>
                <!-- 編集ページへのリンク -->
                <a
                    :href="'/stressor-and-responses/' + itemId + '/edit'"
                    class="text-rose-600 hover:text-rose-800 transition-colors p-2 rounded hover:bg-rose-50"
                    title="編集する"
                >
                    ✏️
                </a>
                <button
                    @click="deleteItem()"
                    class="text-red-400 hover:text-red-600 transition-colors p-2 rounded hover:bg-red-50"
                    title="削除"
                >
                    🗑️
                </button>
            </div>
        </div>

        <!-- 詳細表示 -->
        <div class="space-y-4">
            <!-- ストレッサー -->
            <div class="bg-rose-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-rose-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-rose-500 text-white text-xs">1</span>
                    ストレッサー
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" x-text="item?.stressor || '未入力'"></p>
            </div>

            <!-- ストレス反応セクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="text-rose-500">⚡</span>
                    ストレス反応
                </h3>
                
                <div class="space-y-4">
                    <!-- 認知（自動思考） -->
                    <div class="bg-amber-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-amber-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 text-white text-xs">💭</span>
                            認知（自動思考）
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.cognition ? 'text-gray-400' : ''" x-text="item?.cognition || '未入力'"></p>
                    </div>

                    <!-- 気分・感情 -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-blue-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-500 text-white text-xs">💙</span>
                            気分・感情
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.mood ? 'text-gray-400' : ''" x-text="item?.mood || '未入力'"></p>
                    </div>

                    <!-- 身体反応 -->
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-green-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-500 text-white text-xs">🫀</span>
                            身体反応
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.body_reaction ? 'text-gray-400' : ''" x-text="item?.body_reaction || '未入力'"></p>
                    </div>

                    <!-- 行動 -->
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-purple-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-purple-500 text-white text-xs">🏃</span>
                            行動
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.behavior ? 'text-gray-400' : ''" x-text="item?.behavior || '未入力'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- エラー -->
    <div x-show="!loading && !item" class="text-center py-16 bg-white rounded-xl shadow-md">
        <p class="text-6xl mb-4">😢</p>
        <p class="text-gray-600 text-lg mb-2">データが見つかりません</p>
        <a href="/stressor-and-responses/list" class="inline-block mt-4 text-rose-600 hover:text-rose-800">
            ←
        </a>
    </div>
</div>

<script>
function stressorDetailApp() {
    return {
        item: null,
        loading: true,
        itemId: {{ $itemId }},

        async init() {
            await this.loadItem();
        },

        async loadItem() {
            try {
                const res = await fetch(`/api/stressor-and-responses/${this.itemId}`);
                if (res.ok) {
                    this.item = await res.json();
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        async deleteItem() {
            if (!confirm('この記録を削除しますか？')) return;

            try {
                await fetch(`/api/stressor-and-responses/${this.itemId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                window.location.href = '/stressor-and-responses/list';
            } catch (e) {
                console.error(e);
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
                minute: '2-digit'
            });
        }
    };
}
</script>
@endsection

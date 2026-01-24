@extends('layouts.app')

@section('title', '不安日記詳細')
@section('page-title', '不安日記')

@section('content')
<div x-data="anxietyDiaryDetailApp()" x-init="init()" x-cloak>
    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 mx-auto text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- コンテンツ -->
    <div x-show="!loading && item" class="space-y-4">
        <!-- ヘッダー -->
        <div class="flex items-center justify-between mb-4">
            <a href="/anxiety-diaries/list" class="text-orange-600 hover:text-orange-800 flex items-center gap-1 transition-colors">
                ←
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500" x-text="formatDate(item?.created_at)"></span>
                <!-- 編集ページへのリンク -->
                <a
                    :href="'/anxiety-diaries/' + itemId + '/edit'"
                    class="text-orange-600 hover:text-orange-800 transition-colors p-2 rounded hover:bg-orange-50"
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
            <!-- 状況 -->
            <div class="bg-amber-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-amber-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 text-white text-xs">1</span>
                    状況
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" x-text="item?.situation || '未入力'"></p>
            </div>

            <!-- どんな不安が思い浮かんだか -->
            <div class="bg-orange-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-orange-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-orange-500 text-white text-xs">2</span>
                    どんな不安が思い浮かんだか
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.anxiety_thought ? 'text-gray-400' : ''" x-text="item?.anxiety_thought || '未入力'"></p>
            </div>

            <!-- 実際にどうなったか -->
            <div class="bg-green-50 rounded-lg p-4 border-2 border-green-200">
                <div class="text-xs font-semibold text-green-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-500 text-white text-xs">3</span>
                    実際にどうなったか ✨
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere font-medium" :class="!item?.actual_outcome ? 'text-gray-400' : ''" x-text="item?.actual_outcome || '未入力'"></p>
            </div>
        </div>
    </div>

    <!-- エラー -->
    <div x-show="!loading && !item" class="text-center py-16 bg-white rounded-xl shadow-md">
        <p class="text-6xl mb-4">😢</p>
        <p class="text-gray-600 text-lg mb-2">不安日記が見つかりません</p>
        <a href="/anxiety-diaries/list" class="inline-block mt-4 text-orange-600 hover:text-orange-800">
            ←
        </a>
    </div>
</div>

<script>
function anxietyDiaryDetailApp() {
    return {
        item: null,
        loading: true,
        itemId: {{ $itemId }},

        async init() {
            await this.loadItem();
        },

        async loadItem() {
            try {
                const res = await fetch(`/api/anxiety-diaries/${this.itemId}`);
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
            if (!confirm('この不安日記を削除しますか？')) return;

            try {
                await fetch(`/api/anxiety-diaries/${this.itemId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                window.location.href = '/anxiety-diaries/list';
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

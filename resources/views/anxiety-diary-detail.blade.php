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

    <!-- 削除確認モーダル -->
    <div
        x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="showDeleteModal = false"
    >
        <!-- オーバーレイ -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showDeleteModal = false"></div>

        <!-- モーダルコンテンツ -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl"
                @click.stop
            >
                <!-- ヘッダー -->
                <div class="bg-gradient-to-r from-red-500 to-rose-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        削除の確認
                    </h3>
                </div>

                <!-- コンテンツ -->
                <div class="px-6 py-5">
                    <p class="text-gray-700 text-base mb-4">
                        この不安日記を削除しますか？
                    </p>
                    <p class="text-sm text-gray-500">
                        この操作は取り消せません。
                    </p>
                </div>

                <!-- フッター -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="flex-1 py-2.5 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                    >
                        キャンセル
                    </button>
                    <button
                        type="button"
                        @click="confirmDelete()"
                        :disabled="deleting"
                        class="flex-1 py-2.5 px-4 bg-gradient-to-r from-red-500 to-rose-500 text-white rounded-lg font-medium hover:from-red-600 hover:to-rose-600 transition-colors disabled:opacity-50"
                    >
                        <span x-show="!deleting">削除する</span>
                        <span x-show="deleting" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            削除中...
                        </span>
                    </button>
                </div>
            </div>
        </div>
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
                    @click="showDeleteModal = true"
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
        showDeleteModal: false,
        deleting: false,

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

        async confirmDelete() {
            this.deleting = true;
            try {
                await fetch(`/api/anxiety-diaries/${this.itemId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                window.location.href = '/anxiety-diaries/list';
            } catch (e) {
                console.error(e);
                this.deleting = false;
                this.showDeleteModal = false;
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

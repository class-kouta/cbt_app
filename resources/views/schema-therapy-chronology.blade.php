@extends('layouts.app')

@section('title', '年表一覧 - ココロの避難所')
@section('page-title', '年表')

@section('content')
<div x-data="chronologyListApp()" x-init="init()" x-cloak>
    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="item in chronologies" :key="item.id">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <a
                    :href="'/schema-therapy/chronology/' + item.id + '/edit'"
                    class="block hover:bg-gray-50 transition-colors"
                >
                    <div class="p-4">
                        <!-- いつ -->
                        <div class="mb-2">
                            <span class="text-xs text-gray-500">いつ</span>
                            <p class="text-gray-800 break-words overflow-wrap-anywhere text-sm" x-text="item.when_period"></p>
                        </div>
                        <!-- 環境・出来事 -->
                        <div x-show="item.environment_event" class="mb-2">
                            <span class="text-xs text-gray-500">環境・出来事</span>
                            <p class="text-gray-800 break-words overflow-wrap-anywhere text-sm whitespace-pre-wrap" x-text="item.environment_event"></p>
                        </div>
                        <!-- 体験・感じたこと -->
                        <div x-show="item.experience_feeling">
                            <span class="text-xs text-gray-500">体験・感じたこと・思ったこと</span>
                            <p class="text-gray-600 break-words overflow-wrap-anywhere text-sm whitespace-pre-wrap" x-text="item.experience_feeling"></p>
                        </div>
                    </div>
                </a>
                <!-- 削除ボタン -->
                <div class="px-4 pb-3 flex justify-end">
                    <button
                        @click="confirmDelete(item.id)"
                        class="text-red-400 hover:text-red-600 transition-colors p-2 rounded hover:bg-red-50"
                        title="削除"
                    >
                        🗑️
                    </button>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-1"></div>
            </div>
        </template>

        <!-- ローディング中 -->
        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <div class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600 text-lg">読み込み中...</p>
            </div>
        </div>

        <!-- エラー状態 -->
        <div x-show="!loading && errorOccurred" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">😢</p>
            <p class="text-gray-600 text-lg mb-2">データの取得に失敗しました</p>
            <button
                @click="loadChronologies()"
                class="inline-block mt-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white py-2 px-6 rounded-lg font-medium hover:from-green-600 hover:to-emerald-600 transition-all"
            >
                再読み込み
            </button>
        </div>

        <!-- 空の状態 -->
        <div x-show="!loading && !errorOccurred && chronologies.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">📜</p>
            <p class="text-gray-600 text-lg mb-2">まだ年表がありません</p>
            <a href="/schema-therapy/chronology/create" class="inline-block mt-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white py-2 px-6 rounded-lg font-medium hover:from-green-600 hover:to-emerald-600 transition-all">
                年表を記入する
            </a>
        </div>
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
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showDeleteModal = false"></div>
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
                <div class="bg-gradient-to-r from-red-500 to-rose-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        🗑️ 削除確認
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <p class="text-gray-700 text-base">この年表を削除しますか？</p>
                    <p class="text-sm text-gray-500 mt-2">この操作は取り消せません。</p>
                </div>
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
                        @click="executeDelete()"
                        :disabled="deleting"
                        class="flex-1 py-2.5 px-4 bg-gradient-to-r from-red-500 to-rose-500 text-white rounded-lg font-medium hover:from-red-600 hover:to-rose-600 transition-colors disabled:opacity-50"
                    >
                        <span x-show="!deleting">削除する</span>
                        <span x-show="deleting" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
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

    <!-- 削除成功トースト -->
    <div
        x-show="showDeleteToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2"
    >
        <span>🗑️</span>
        <span>削除しました</span>
    </div>

    <!-- 新規作成ボタン（フローティング） -->
    <a
        href="/schema-therapy/chronology/create"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-green-600 hover:to-emerald-600 transition-all z-30"
        title="新しい年表を作成"
    >
        ＋
    </a>
</div>

<script>
function chronologyListApp() {
    return {
        chronologies: [],
        loading: true,
        errorOccurred: false,
        showDeleteModal: false,
        deleteTargetId: null,
        deleting: false,
        showDeleteToast: false,

        async init() {
            await this.loadChronologies();
        },

        async loadChronologies() {
            this.loading = true;
            this.errorOccurred = false;
            try {
                const res = await fetch('/api/chronologies');
                if (res.ok) {
                    this.chronologies = await res.json();
                } else {
                    this.errorOccurred = true;
                }
            } catch (error) {
                this.errorOccurred = true;
            } finally {
                this.loading = false;
            }
        },

        confirmDelete(id) {
            this.deleteTargetId = id;
            this.showDeleteModal = true;
        },

        async executeDelete() {
            if (!this.deleteTargetId || this.deleting) return;

            this.deleting = true;
            try {
                const res = await fetch(`/api/chronologies/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                if (res.ok || res.status === 204) {
                    this.chronologies = this.chronologies.filter(c => c.id !== this.deleteTargetId);
                    this.showDeleteModal = false;
                    this.showDeleteToast = true;
                    setTimeout(() => {
                        this.showDeleteToast = false;
                    }, 2000);
                }
            } catch (error) {
                // エラー詳細はセキュリティ上コンソールに出力しない
            } finally {
                this.deleting = false;
                this.deleteTargetId = null;
            }
        }
    };
}
</script>
@endsection

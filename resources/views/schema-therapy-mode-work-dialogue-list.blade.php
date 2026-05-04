@extends('layouts.app')

@section('title', 'モードワーク 対話のワーク - ' . config('app.name'))
@section('page-title', '対話のワーク')

@section('content')
<div x-data="modeDialogueWorkListApp()" x-init="init()" x-cloak>
    <!-- ヘッダー -->
    <div class="mb-4 flex justify-between items-center">
        <a href="/schema-therapy/mode-work" class="text-green-600 hover:text-green-800 flex items-center gap-1 text-sm">
            ← モードワーク
        </a>
        <span x-show="!loading && !errorOccurred && items.length > 0" class="text-sm text-gray-500">
            🔍 全 <span class="font-semibold text-gray-700" x-text="items.length"></span> 件
        </span>
    </div>

    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="item in items" :key="item.id">
            <a
                :href="'/schema-therapy/mode-work/dialogue/' + item.id + '/edit'"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-teal-300"
            >
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold text-teal-700 mb-1">
                            <span x-text="item.mode_name + 'との対話'"></span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1" x-text="formatDate(item.created_at)"></div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <div class="bg-gradient-to-r from-teal-500 to-emerald-500 h-1"></div>
            </a>
        </template>

        <!-- ローディング中 -->
        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <div class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-8 w-8 text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                @click="loadItems()"
                class="inline-block mt-4 bg-gradient-to-r from-teal-500 to-emerald-500 text-white py-2 px-6 rounded-lg font-medium hover:from-teal-600 hover:to-emerald-600 transition-all"
            >
                再読み込み
            </button>
        </div>

        <!-- 空の状態 -->
        <div x-show="!loading && !errorOccurred && items.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">💬</p>
            <p class="text-gray-600 text-lg mb-2">まだ対話のワークがありません</p>
            <a href="/schema-therapy/mode-work/dialogue/create" class="inline-block mt-4 bg-gradient-to-r from-teal-500 to-emerald-500 text-white py-2 px-6 rounded-lg font-medium hover:from-teal-600 hover:to-emerald-600 transition-all">
                対話のワークを始める
            </a>
        </div>
    </div>

    <!-- 新規作成ボタン（フローティング） -->
    <a
        href="/schema-therapy/mode-work/dialogue/create"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-teal-500 to-emerald-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-teal-600 hover:to-emerald-600 transition-all z-30"
        title="新しい対話のワークを作成"
    >
        ＋
    </a>
</div>

<script>
function modeDialogueWorkListApp() {
    return {
        items: [],
        loading: true,
        errorOccurred: false,

        async init() {
            await this.loadItems();
        },

        async loadItems() {
            this.loading = true;
            this.errorOccurred = false;
            try {
                const res = await apiFetch('/api/mode-dialogue-works');
                if (res.ok) {
                    this.items = await res.json();
                } else {
                    this.errorOccurred = true;
                }
            } catch (error) {
                this.errorOccurred = true;
            } finally {
                this.loading = false;
            }
        },

        formatDate(dateString) {
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

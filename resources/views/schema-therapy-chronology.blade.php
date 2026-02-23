@extends('layouts.app')

@section('title', '年表一覧 - ココロの避難所')
@section('page-title', '年表')

@section('content')
<div x-data="chronologyListApp()" x-init="init()" x-cloak>
    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="item in chronologies" :key="item.id">
            <a
                :href="'/schema-therapy/chronology/' + item.id + '/edit'"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-green-300"
            >
                <div class="p-4">
                    <!-- いつ -->
                    <div class="text-sm text-green-600 font-medium mb-2" x-text="item.when_period"></div>
                    <!-- 環境・出来事 -->
                    <div x-show="item.environment_event" class="mb-2">
                        <span class="text-xs text-gray-500">環境・出来事</span>
                        <p class="text-gray-800 line-clamp-2 break-words overflow-wrap-anywhere text-sm" x-text="item.environment_event"></p>
                    </div>
                    <!-- 体験・感じたこと -->
                    <div x-show="item.experience_feeling">
                        <span class="text-xs text-gray-500">体験・感じたこと</span>
                        <p class="text-gray-600 line-clamp-2 break-words overflow-wrap-anywhere text-sm" x-text="item.experience_feeling"></p>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-1"></div>
            </a>
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

        <!-- 空の状態 -->
        <div x-show="!loading && chronologies.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">📜</p>
            <p class="text-gray-600 text-lg mb-2">まだ年表がありません</p>
            <a href="/schema-therapy/chronology/create" class="inline-block mt-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white py-2 px-6 rounded-lg font-medium hover:from-green-600 hover:to-emerald-600 transition-all">
                年表を記入する
            </a>
        </div>
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

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
function chronologyListApp() {
    return {
        chronologies: [],
        loading: true,

        async init() {
            await this.loadChronologies();
        },

        async loadChronologies() {
            this.loading = true;
            try {
                const res = await fetch('/api/chronologies');
                if (res.ok) {
                    this.chronologies = await res.json();
                }
            } catch (error) {
                console.error('年表の取得に失敗しました:', error);
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endsection

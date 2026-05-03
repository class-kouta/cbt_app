@extends('layouts.app')

@section('title', '年表一覧 - ココケア')
@section('page-title', '年表')

@section('content')
<div x-data="chronologyListApp()" x-init="init()" x-cloak>
    <!-- カウント表示 & CSV出力 -->
    <div x-show="!loading && !errorOccurred && chronologies.length > 0" class="mb-4 flex justify-between items-center">
        <span class="text-sm text-gray-500">📜 全 <span class="font-semibold text-gray-700" x-text="chronologies.length"></span> 件</span>
        <button
            @click="exportCsv()"
            :disabled="exporting"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-green-400 transition-all text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <template x-if="!exporting">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </template>
            <template x-if="exporting">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
            </template>
            <span x-text="exporting ? 'エクスポート中...' : 'CSV出力'"></span>
        </button>
    </div>

    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="item in chronologies" :key="item.id">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <a
                    :href="'/schema-therapy/chronology/' + item.id + '/edit'"
                    class="block hover:bg-gray-50 transition-colors"
                >
                    <div class="p-4">
                        <!-- いつ & タグ -->
                        <div class="mb-2 flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <span class="text-xs text-gray-500">いつ</span>
                                <p class="text-gray-800 break-words overflow-wrap-anywhere text-sm" x-text="item.when_period"></p>
                            </div>
                            <span
                                x-show="item.sentiment_type === 'positive'"
                                class="shrink-0 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200"
                            >
                                😊 ポジティブ
                            </span>
                            <span
                                x-show="item.sentiment_type === 'negative'"
                                class="shrink-0 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 border border-blue-200"
                            >
                                😢 ネガティブ
                            </span>
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
                <div
                    class="h-1"
                    :class="{
                        'bg-gradient-to-r from-orange-400 to-amber-400': item.sentiment_type === 'positive',
                        'bg-gradient-to-r from-blue-400 to-indigo-400': item.sentiment_type === 'negative',
                        'bg-gradient-to-r from-green-500 to-emerald-500': !item.sentiment_type
                    }"
                ></div>
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
        exporting: false,

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

        async exportCsv() {
            this.exporting = true;
            try {
                await exportCsvFromApi(
                    '/api/chronologies/export/csv',
                    {},
                    'chronologies.csv',
                    '年表'
                );
            } catch (error) {
                alert('CSVエクスポートに失敗しました');
            } finally {
                this.exporting = false;
            }
        }
    };
}
</script>
@endsection

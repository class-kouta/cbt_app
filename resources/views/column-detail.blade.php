@extends('layouts.app')

@section('title', 'コラム詳細')

@section('content')
<div x-data="columnDetailApp()" x-init="init()" x-cloak>
    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 mx-auto text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- コンテンツ -->
    <div x-show="!loading && column" class="space-y-4">
        <!-- ヘッダー -->
        <div class="flex items-center justify-between mb-4">
            <a href="/columns/list" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors">
                ← 一覧に戻る
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500" x-text="formatDate(column?.created_at)"></span>
                <button
                    @click="deleteColumn()"
                    class="text-red-400 hover:text-red-600 transition-colors p-2 rounded hover:bg-red-50"
                    title="削除"
                >
                    🗑️
                </button>
            </div>
        </div>

        <!-- コラム内容 -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2"></div>
            <div class="p-5 space-y-4">
                <!-- 状況 -->
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-indigo-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-500 text-white text-xs">1</span>
                        状況
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap" x-text="column?.situation"></p>
                </div>

                <!-- 気分 -->
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-indigo-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-500 text-white text-xs">2</span>
                        気分
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap" x-text="column?.mood"></p>
                </div>

                <!-- 自動思考 -->
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-indigo-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-500 text-white text-xs">3</span>
                        自動思考
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap" x-text="column?.automatic_thought"></p>
                </div>

                <!-- 根拠と反証 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 根拠 -->
                    <div class="bg-amber-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-amber-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 text-white text-xs">4</span>
                            根拠
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap" x-text="column?.evidence"></p>
                    </div>

                    <!-- 反証 -->
                    <div class="bg-teal-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-teal-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-teal-500 text-white text-xs">5</span>
                            反証
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap" x-text="column?.counter_evidence"></p>
                    </div>
                </div>

                <!-- 適応的思考 -->
                <div class="bg-emerald-50 rounded-lg p-4 border-2 border-emerald-200">
                    <div class="text-xs font-semibold text-emerald-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-xs">6</span>
                        適応的思考 ✨
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap font-medium" x-text="column?.adaptive_thought"></p>
                </div>

                <!-- いまの気分 -->
                <div class="bg-pink-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-pink-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-pink-500 text-white text-xs">7</span>
                        いまの気分
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap" x-text="column?.current_mood"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- エラー -->
    <div x-show="!loading && !column" class="text-center py-16 bg-white rounded-xl shadow-md">
        <p class="text-6xl mb-4">😢</p>
        <p class="text-gray-600 text-lg mb-2">コラムが見つかりません</p>
        <a href="/columns/list" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800">
            ← 一覧に戻る
        </a>
    </div>
</div>

<script>
function columnDetailApp() {
    return {
        column: null,
        loading: true,
        columnId: {{ $columnId }},

        async init() {
            await this.loadColumn();
        },

        async loadColumn() {
            try {
                const res = await fetch(`/api/columns/${this.columnId}`);
                if (res.ok) {
                    this.column = await res.json();
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        async deleteColumn() {
            if (!confirm('このコラムを削除しますか？')) return;

            try {
                await fetch(`/api/columns/${this.columnId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                window.location.href = '/columns/list';
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

@extends('layouts.app')

@section('title', 'コラム詳細')
@section('page-title', 'コラム法')

@section('content')
<div x-data="columnDetailApp()" x-init="init()" x-cloak>
    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 mx-auto text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- コンテンツ -->
    <div x-show="!loading && column" class="space-y-4">
        <!-- ヘッダー -->
        <div class="flex items-center justify-between mb-4">
            <a href="/columns/list" class="text-teal-600 hover:text-teal-800 flex items-center gap-1 transition-colors">
                ←
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500" x-text="formatDate(column?.created_at)"></span>
                <!-- 編集ページへのリンク -->
                <a
                    :href="'/columns/' + columnId + '/edit'"
                    class="text-teal-600 hover:text-teal-800 transition-colors p-2 rounded hover:bg-teal-50"
                    title="編集する"
                >
                    ✏️
                </a>
                <button
                    @click="deleteColumn()"
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
                <div class="bg-emerald-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-emerald-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-xs">1</span>
                        状況
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" x-text="column?.situation || '未入力'"></p>
                </div>

                <!-- 気分 -->
                <div class="bg-emerald-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-emerald-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-xs">2</span>
                        気分
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.mood ? 'text-gray-400' : ''" x-text="column?.mood || '未入力'"></p>
                </div>

                <!-- 自動思考 -->
                <div class="bg-emerald-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-emerald-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-xs">3</span>
                        自動思考
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.automatic_thought ? 'text-gray-400' : ''" x-text="column?.automatic_thought || '未入力'"></p>
                </div>

                <!-- 根拠と反証 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 根拠 -->
                    <div class="bg-teal-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-teal-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-teal-500 text-white text-xs">4</span>
                            根拠
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.evidence ? 'text-gray-400' : ''" x-text="column?.evidence || '未入力'"></p>
                    </div>

                    <!-- 反証 -->
                    <div class="bg-cyan-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-cyan-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-cyan-600 text-white text-xs">5</span>
                            反証
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.counter_evidence ? 'text-gray-400' : ''" x-text="column?.counter_evidence || '未入力'"></p>
                    </div>
                </div>

                <!-- 適応的思考 -->
                <div class="bg-green-50 rounded-lg p-4 border-2 border-green-200">
                    <div class="text-xs font-semibold text-green-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-500 text-white text-xs">6</span>
                        適応的思考 ✨
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere font-medium" :class="!column?.adaptive_thought ? 'text-gray-400' : ''" x-text="column?.adaptive_thought || '未入力'"></p>
                </div>

                <!-- いまの気分 -->
                <div class="bg-lime-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-lime-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-lime-500 text-white text-xs">7</span>
                        いまの気分
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.current_mood ? 'text-gray-400' : ''" x-text="column?.current_mood || '未入力'"></p>
                </div>

                <!-- 備考 -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-500 text-white text-xs">📝</span>
                        備考
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.notes ? 'text-gray-400' : ''" x-text="column?.notes || '未入力'"></p>
                </div>
            </div>
    </div>

    <!-- エラー -->
    <div x-show="!loading && !column" class="text-center py-16 bg-white rounded-xl shadow-md">
        <p class="text-6xl mb-4">😢</p>
        <p class="text-gray-600 text-lg mb-2">コラムが見つかりません</p>
        <a href="/columns/list" class="inline-block mt-4 text-teal-600 hover:text-teal-800">
            ←
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

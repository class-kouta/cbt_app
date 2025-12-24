@extends('layouts.app')

@section('title', '問題解決法詳細')
@section('page-title', '問題解決法')

@section('content')
<div x-data="problemSolvingDetailApp({{ $itemId }})" x-init="init()" x-cloak>
    <!-- ヘッダー -->
    <div class="flex justify-between items-center mb-4">
        <a href="/problem-solvings/list" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
            ← 一覧に戻る
        </a>
        <div class="flex gap-2">
            <a
                :href="'/problem-solvings/' + itemId + '/edit'"
                class="text-emerald-600 hover:text-emerald-800 transition-colors p-2 rounded hover:bg-emerald-50"
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

    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <!-- 詳細表示 -->
    <div x-show="!loading && item">

        <div class="space-y-6">
            <!-- Step 1: 問題状況 -->
            <div class="bg-emerald-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-emerald-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-xs">1</span>
                    問題状況
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words" x-text="item?.problem_situation"></p>
            </div>

            <!-- Step 2: 改善イメージ -->
            <div class="bg-teal-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-teal-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-teal-500 text-white text-xs">2</span>
                    改善イメージ
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words" :class="!item?.improved_image ? 'text-gray-400' : ''" x-text="item?.improved_image || '未入力'"></p>
            </div>

            <!-- Step 3: 解決策 -->
            <div class="bg-cyan-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-cyan-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-cyan-600 text-white text-xs">3</span>
                    解決策
                </div>
                <div x-show="item?.solutions && item.solutions.length > 0" class="space-y-3">
                    <template x-for="(solution, index) in item?.solutions" :key="solution.id">
                        <div class="border border-cyan-200 rounded-lg p-3 bg-white">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm text-gray-500 font-medium" x-text="'解決策 ' + (index + 1)"></span>
                            </div>
                            <p class="text-gray-800 mb-2" x-text="solution.content"></p>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-xs text-gray-600">効果的か：</span>
                                    <span class="font-medium" x-text="solution.effectiveness !== null ? solution.effectiveness + '%' : '-'"></span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-600">実行可能か：</span>
                                    <span class="font-medium" x-text="solution.feasibility !== null ? solution.feasibility + '%' : '-'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <p x-show="!item?.solutions || item.solutions.length === 0" class="text-gray-400">未入力</p>
            </div>

            <!-- Step 4: 実行計画 -->
            <div class="bg-teal-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-teal-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-teal-500 text-white text-xs">4</span>
                    実行計画
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words" :class="!item?.action_plan ? 'text-gray-400' : ''" x-text="item?.action_plan || '未入力'"></p>
            </div>

            <!-- Step 5: 振り返り -->
            <div class="bg-lime-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-lime-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-lime-500 text-white text-xs">5</span>
                    振り返り
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words" :class="!item?.reflection ? 'text-gray-400' : ''" x-text="item?.reflection || '未入力'"></p>
            </div>
        </div>
    </div>
</div>

<script>
function problemSolvingDetailApp(itemId) {
    return {
        itemId: itemId,
        item: null,
        loading: true,

        async init() {
            await this.loadItem();
        },

        async loadItem() {
            this.loading = true;
            try {
                const res = await fetch(`/api/problem-solvings/${this.itemId}`);
                if (res.ok) {
                    this.item = await res.json();
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        async deleteItem() {
            if (!confirm('この記録を削除しますか？')) return;

            try {
                const res = await fetch(`/api/problem-solvings/${this.itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (res.ok) {
                    window.location.href = '/problem-solvings/list';
                } else {
                    alert('削除に失敗しました');
                }
            } catch (error) {
                console.error(error);
                alert('削除に失敗しました');
            }
        }
    };
}
</script>
@endsection

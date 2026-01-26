@extends('layouts.app')

@section('title', '計画一覧')
@section('page-title', '計画一覧')

@section('content')
<div x-data="plansListApp()" x-init="init()" x-cloak>
    <!-- フィルター -->
    <div class="mb-4 flex gap-2">
        <button
            @click="filter = 'all'"
            class="px-4 py-2 rounded-full text-sm font-medium transition-all"
            :class="filter === 'all' ? 'bg-emerald-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
        >
            すべて
        </button>
        <button
            @click="filter = 'pending'"
            class="px-4 py-2 rounded-full text-sm font-medium transition-all"
            :class="filter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
        >
            振り返り待ち
        </button>
        <button
            @click="filter = 'completed'"
            class="px-4 py-2 rounded-full text-sm font-medium transition-all"
            :class="filter === 'completed' ? 'bg-green-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
        >
            振り返り済み
        </button>
    </div>

    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="plan in filteredPlans" :key="plan.planId">
            <a
                :href="'/problem-solvings/' + plan.problemSolvingId"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-emerald-300"
            >
                <div class="p-4">
                    <!-- ステータスバッジ -->
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span
                                x-show="plan.hasReflection"
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"
                            >
                                ✓ 振り返り済み
                            </span>
                            <span
                                x-show="!plan.hasReflection"
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700"
                            >
                                振り返り待ち
                            </span>
                        </div>
                        <span class="text-xs text-gray-400" x-text="formatDate(plan.createdAt)"></span>
                    </div>

                    <!-- 問題状況（どの問題に紐づくか） -->
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 block mb-1">📋 問題状況</span>
                        <p class="text-gray-700 text-sm line-clamp-1 break-words" x-text="plan.problemSituation"></p>
                    </div>

                    <!-- 実行計画 -->
                    <div class="mb-3">
                        <span class="text-xs text-emerald-600 font-medium block mb-1">📝 実行計画</span>
                        <p class="text-gray-800 whitespace-pre-wrap break-words" x-text="plan.actionPlan || '未入力'"></p>
                    </div>

                    <!-- 振り返り（ある場合のみ表示） -->
                    <div x-show="plan.hasReflection" class="bg-green-50 rounded-lg p-2">
                        <span class="text-xs text-green-600 font-medium block mb-1">💭 振り返り</span>
                        <p class="text-gray-800 text-sm line-clamp-2 break-words" x-text="plan.reflection"></p>
                    </div>
                </div>
                <div
                    class="h-1"
                    :class="plan.hasReflection
                        ? 'bg-gradient-to-r from-green-400 to-emerald-500'
                        : 'bg-gradient-to-r from-yellow-400 to-orange-400'"
                ></div>
            </a>
        </template>

        <!-- ローディング中 -->
        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 text-lg mt-2">読み込み中...</p>
        </div>

        <!-- 空の状態 -->
        <div x-show="!loading && allPlans.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">📋</p>
            <p class="text-gray-600 text-lg mb-2">まだ計画がありません</p>
            <a href="/problem-solvings" class="inline-block mt-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-2 px-6 rounded-lg font-medium hover:from-emerald-600 hover:to-teal-600 transition-all">
                問題解決を始める
            </a>
        </div>

        <!-- フィルター結果が空の場合 -->
        <div x-show="!loading && allPlans.length > 0 && filteredPlans.length === 0" class="text-center py-12 bg-white rounded-xl shadow-md">
            <p class="text-4xl mb-4" x-text="filter === 'completed' ? '🎉' : '⏳'"></p>
            <p class="text-gray-600" x-text="filter === 'completed' ? '振り返り済みの計画はまだありません' : '振り返り待ちの計画はありません'"></p>
        </div>
    </div>

    <!-- 新規作成ボタン（フローティング） -->
    <a
        href="/problem-solvings"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-emerald-600 hover:to-teal-600 transition-all"
        title="新しい問題解決を作成"
    >
        ＋
    </a>
</div>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
function plansListApp() {
    return {
        allPlans: [],
        loading: true,
        filter: 'all',

        async init() {
            await this.loadPlans();
        },

        async loadPlans() {
            this.loading = true;
            try {
                const res = await fetch('/api/problem-solvings/plans');
                const plans = await res.json();

                // APIレスポンスをフロント用の形式に変換
                this.allPlans = plans.map(plan => ({
                    planId: plan.id,
                    problemSolvingId: plan.problem_solving_id,
                    problemSituation: plan.problem_situation,
                    planNumber: plan.plan_number,
                    actionPlan: plan.action_plan,
                    reflection: plan.reflection,
                    hasReflection: plan.reflection && plan.reflection.trim() !== '',
                    createdAt: plan.created_at
                }));
            } catch (error) {
                console.error('Failed to load plans:', error);
            } finally {
                this.loading = false;
            }
        },

        get filteredPlans() {
            if (this.filter === 'all') {
                return this.allPlans;
            } else if (this.filter === 'completed') {
                return this.allPlans.filter(p => p.hasReflection);
            } else if (this.filter === 'pending') {
                return this.allPlans.filter(p => !p.hasReflection);
            }
            return this.allPlans;
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    };
}
</script>
@endsection

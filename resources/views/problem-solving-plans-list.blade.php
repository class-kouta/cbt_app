@extends('layouts.app')

@section('title', '計画一覧')
@section('page-title', '計画一覧')

@section('content')
<div x-data="plansListApp()" x-init="init()" x-cloak>
    <!-- 検索フォーム -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <!-- キーワード検索 -->
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">キーワード検索</label>
            <input
                type="text"
                x-model="keyword"
                @keyup.enter="search()"
                placeholder="実行計画、振り返り、問題状況で検索..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base"
            >
        </div>

        <!-- 改善レベル範囲検索 -->
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">📊 改善レベルで絞り込み</label>
            <div class="flex items-center gap-2">
                <select
                    x-model.number="improvementLevelMin"
                    class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm bg-white"
                    :class="hasRangeError ? 'border-red-400' : 'border-gray-300'"
                >
                    <template x-for="n in 10" :key="'min-' + n">
                        <option :value="n" x-text="n"></option>
                    </template>
                </select>
                <span class="text-gray-500 text-sm font-medium">〜</span>
                <select
                    x-model.number="improvementLevelMax"
                    class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm bg-white"
                    :class="hasRangeError ? 'border-red-400' : 'border-gray-300'"
                >
                    <template x-for="n in 10" :key="'max-' + n">
                        <option :value="n" x-text="n"></option>
                    </template>
                </select>
            </div>
            <p
                x-show="hasRangeError"
                class="text-red-500 text-xs mt-1"
            >
                改善レベルの下限は上限以下にしてください
            </p>
        </div>

        <!-- 検索ボタン & 検索条件クリア -->
        <div class="mt-3 pt-3 border-t border-gray-200 flex items-center gap-3">
            <button
                @click="search()"
                :disabled="hasRangeError"
                class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:from-emerald-600 hover:to-teal-600 transition-all text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                検索
            </button>
            <button
                x-show="hasSearchCondition"
                @click="clearSearch()"
                class="text-sm text-gray-500 hover:text-gray-700 underline"
            >
                検索条件をクリア
            </button>
        </div>
    </div>

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
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-green-600 font-medium">💭 振り返り</span>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold"
                                :class="{
                                    'bg-gray-100 text-gray-500': !plan.improvementLevel,
                                    'bg-red-100 text-red-700': plan.improvementLevel && plan.improvementLevel <= 3,
                                    'bg-yellow-100 text-yellow-700': plan.improvementLevel && plan.improvementLevel >= 4 && plan.improvementLevel <= 6,
                                    'bg-green-100 text-green-700': plan.improvementLevel && plan.improvementLevel >= 7
                                }"
                            >
                                📊 改善Lv.<span x-text="plan.improvementLevel || '-'"></span>
                            </span>
                        </div>
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

        <!-- 空の状態（検索条件なし） -->
        <div x-show="!loading && allPlans.length === 0 && !hasSearchCondition" class="text-center py-16 bg-white rounded-xl shadow-md">
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

        <!-- 検索結果が空の場合 -->
        <div x-show="!loading && allPlans.length === 0 && hasSearchCondition" class="text-center py-12 bg-white rounded-xl shadow-md">
            <p class="text-4xl mb-4">🔍</p>
            <p class="text-gray-600 mb-2">条件に一致する計画が見つかりませんでした</p>
            <button
                @click="clearSearch()"
                class="inline-block mt-2 text-emerald-600 hover:text-emerald-700 underline text-sm"
            >
                検索条件をクリアする
            </button>
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
        filter: new URLSearchParams(window.location.search).get('filter') || 'all',
        keyword: '',
        improvementLevelMin: 1,
        improvementLevelMax: 10,

        async init() {
            await this.loadPlans();
        },

        async loadPlans() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.keyword) {
                    params.append('keyword', this.keyword);
                }
                if (this.improvementLevelMin !== 1 || this.improvementLevelMax !== 10) {
                    params.append('improvement_level_min', this.improvementLevelMin);
                    params.append('improvement_level_max', this.improvementLevelMax);
                }

                const url = '/api/problem-solvings/plans' + (params.toString() ? '?' + params.toString() : '');
                const res = await fetch(url);
                const plans = await res.json();

                this.allPlans = plans.map(plan => ({
                    planId: plan.id,
                    problemSolvingId: plan.problem_solving_id,
                    problemSituation: plan.problem_situation,
                    planNumber: plan.plan_number,
                    actionPlan: plan.action_plan,
                    reflection: plan.reflection,
                    hasReflection: plan.reflection && plan.reflection.trim() !== '',
                    improvementLevel: plan.improvement_level,
                    createdAt: plan.created_at
                }));
            } catch (error) {
                console.error('Failed to load plans:', error);
            } finally {
                this.loading = false;
            }
        },

        async search() {
            if (this.hasRangeError) return;
            await this.loadPlans();
        },

        async clearSearch() {
            this.keyword = '';
            this.improvementLevelMin = 1;
            this.improvementLevelMax = 10;
            await this.loadPlans();
        },

        get hasSearchCondition() {
            return this.keyword !== '' || this.improvementLevelMin !== 1 || this.improvementLevelMax !== 10;
        },

        get hasRangeError() {
            return this.improvementLevelMin > this.improvementLevelMax;
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

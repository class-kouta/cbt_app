@extends('layouts.app')

@section('title', '振り返り一覧')
@section('page-title', '振り返り一覧')

@section('content')
<div x-data="reflectionsListApp()" x-init="init()" x-cloak>
    <div class="bg-white rounded-xl shadow-md p-4 mb-4 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">問題状況</label>
            <select x-model="problemSolvingId"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-base bg-white">
                <option value="">すべて</option>
                <template x-for="ps in problemSolvings" :key="ps.id">
                    <option :value="ps.id" x-text="ps.problem_situation"></option>
                </template>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ステータス</label>
            <select x-model="filter"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-base bg-white">
                <option value="all">すべて</option>
                <option value="pending">振り返り待ち</option>
                <option value="completed">振り返り済み</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">キーワード検索</label>
            <input
                type="text"
                x-model="keyword"
                @keyup.enter="search()"
                placeholder="実行計画、振り返り、問題状況で検索..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base"
            >
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">改善レベルで絞り込み</label>
            <div class="flex items-center gap-2">
                <select
                    x-model.number="improvementLevelMin"
                    class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-base bg-white"
                    :class="hasRangeError ? 'border-red-400' : 'border-gray-300'"
                >
                    <template x-for="n in 10" :key="'min-' + n">
                        <option :value="n" x-text="n"></option>
                    </template>
                </select>
                <span class="text-gray-500 text-sm font-medium">〜</span>
                <select
                    x-model.number="improvementLevelMax"
                    class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-base bg-white"
                    :class="hasRangeError ? 'border-red-400' : 'border-gray-300'"
                >
                    <template x-for="n in 10" :key="'max-' + n">
                        <option :value="n" x-text="n"></option>
                    </template>
                </select>
            </div>
            <p x-show="hasRangeError" class="text-red-500 text-xs mt-1">
                改善レベルの下限は上限以下にしてください
            </p>
        </div>

        <div class="pt-2 border-t border-gray-200 flex items-center gap-3">
            <button
                @click="search()"
                :disabled="hasRangeError"
                class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                絞り込む
            </button>
            <button
                x-show="hasSearchCondition"
                @click="clearSearch()"
                class="text-sm text-gray-500 hover:text-gray-700 underline"
            >
                絞り込みをクリア
            </button>
        </div>
    </div>

    <div class="space-y-3">
        <template x-for="plan in allPlans" :key="plan.planId">
            <a
                :href="'/problem-solvings/plans/' + plan.planId"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-emerald-300"
            >
                <div class="p-4">
                    <div class="flex items-center justify-end mb-2">
                        <span class="text-xs text-gray-400" x-text="formatDate(plan.createdAt)"></span>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">問題状況</span>
                        <p class="text-gray-700 text-sm line-clamp-1 break-words" x-text="plan.problemSituation"></p>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs text-emerald-600">実行計画</span>
                        <p class="text-gray-800 text-sm line-clamp-2 whitespace-pre-wrap break-words" x-text="plan.actionPlan"></p>
                    </div>
                    <div x-show="plan.hasReflection" class="mb-2">
                        <span class="text-xs text-gray-500">振り返り</span>
                        <p class="text-gray-800 text-sm line-clamp-2 whitespace-pre-wrap break-words" x-text="plan.reflection"></p>
                    </div>
                    <div x-show="plan.improvementLevel !== null && plan.improvementLevel !== ''">
                        <span class="text-xs text-gray-500">改善レベル</span>
                        <p class="text-sm font-medium text-gray-800" x-text="plan.improvementLevel"></p>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-1"></div>
            </a>
        </template>

        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-gray-600 text-lg mt-2">読み込み中...</p>
        </div>

        <div x-show="!loading && allPlans.length === 0 && !hasSearchCondition" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-gray-600 text-lg mb-2">まだ振り返りがありません</p>
            <a href="/problem-solvings/plans/new" class="inline-block mt-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-2 px-6 rounded-lg font-medium">
                振り返りを記録する
            </a>
        </div>

        <div x-show="!loading && allPlans.length === 0 && hasSearchCondition" class="text-center py-12 bg-white rounded-xl shadow-md">
            <p class="text-gray-600 mb-2">条件に一致する振り返りが見つかりませんでした</p>
            <button @click="clearSearch()" class="inline-block mt-2 text-emerald-600 hover:text-emerald-700 underline text-sm">
                絞り込みをクリアする
            </button>
        </div>

        <x-pagination theme-color-from="emerald-500" theme-color-to="teal-500" theme-border-color="emerald-500" />
    </div>

    <a href="/problem-solvings/plans/new" class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg flex items-center justify-center text-2xl" title="振り返りを記録">＋</a>
</div>

<style>
.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<script>
function reflectionsListApp() {
    return {
        allPlans: [],
        problemSolvings: [],
        problemSolvingId: '',
        loading: true,
        filter: new URLSearchParams(window.location.search).get('filter') || 'all',
        keyword: '',
        improvementLevelMin: 1,
        improvementLevelMax: 10,
        currentPage: 1,
        perPage: 10,
        lastPage: 1,

        async init() {
            await this.loadProblemSolvings();
            await this.loadPlans();
        },

        async loadProblemSolvings() {
            this.problemSolvings = await fetchProblemSolvingOptions();
        },

        async loadPlans() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.keyword) params.append('keyword', this.keyword);
                if (this.improvementLevelMin !== 1 || this.improvementLevelMax !== 10) {
                    params.append('improvement_level_min', this.improvementLevelMin);
                    params.append('improvement_level_max', this.improvementLevelMax);
                }
                if (this.filter && this.filter !== 'all') {
                    params.append('filter', this.filter);
                }
                if (this.problemSolvingId) {
                    params.append('problem_solving_id', this.problemSolvingId);
                }
                params.append('page', this.currentPage);
                params.append('per_page', this.perPage);

                const res = await apiFetch('/api/problem-solvings/plans?' + params.toString());
                const result = await res.json();

                this.allPlans = (result.data || []).map(plan => ({
                    planId: plan.id,
                    problemSituation: plan.problem_situation,
                    actionPlan: plan.action_plan,
                    reflection: plan.reflection,
                    hasReflection: plan.reflection && plan.reflection.trim() !== '',
                    improvementLevel: plan.improvement_level,
                    createdAt: plan.created_at
                }));

                this.lastPage = result.last_page || 1;
            } catch (error) {
                console.error('Failed to load reflections:', error);
            } finally {
                this.loading = false;
            }
        },

        async search() {
            if (this.hasRangeError) return;
            this.currentPage = 1;
            await this.loadPlans();
        },

        async clearSearch() {
            this.keyword = '';
            this.problemSolvingId = '';
            this.filter = 'all';
            this.improvementLevelMin = 1;
            this.improvementLevelMax = 10;
            this.currentPage = 1;
            await this.loadPlans();
        },

        async goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) return;
            this.currentPage = page;
            await this.loadPlans();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        get visiblePages() {
            return calculateVisiblePages(this.currentPage, this.lastPage);
        },

        get hasSearchCondition() {
            return this.keyword !== ''
                || this.problemSolvingId !== ''
                || this.filter !== 'all'
                || this.improvementLevelMin !== 1
                || this.improvementLevelMax !== 10;
        },

        get hasRangeError() {
            return this.improvementLevelMin > this.improvementLevelMax;
        },

        formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    };
}
</script>
@endsection

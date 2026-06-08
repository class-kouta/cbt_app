@extends('layouts.app')

@section('title', '実施記録一覧')
@section('page-title', '実施記録一覧')

@section('content')
<div x-data="exposureSessionsListApp()" x-init="init()" x-cloak>
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">キーワード検索</label>
            <input
                type="text"
                x-model="keyword"
                @keyup.enter="search()"
                placeholder="実施計画、振り返り、回避していることで検索..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base"
            >
        </div>
        <div class="mt-3 pt-3 border-t border-gray-200 flex items-center gap-3">
            <button @click="search()" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg text-sm font-medium">
                検索
            </button>
            <button x-show="keyword" @click="clearSearch()" class="text-sm text-gray-500 hover:text-gray-700 underline">
                検索条件をクリア
            </button>
        </div>
    </div>

    <div class="mb-4 flex gap-2">
        <button @click="setFilter('all')" class="px-4 py-2 rounded-full text-sm font-medium transition-all"
            :class="filter === 'all' ? 'bg-emerald-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'">すべて</button>
        <button @click="setFilter('pending')" class="px-4 py-2 rounded-full text-sm font-medium transition-all"
            :class="filter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'">振り返り待ち</button>
        <button @click="setFilter('completed')" class="px-4 py-2 rounded-full text-sm font-medium transition-all"
            :class="filter === 'completed' ? 'bg-green-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'">振り返り済み</button>
    </div>

    <div class="space-y-3">
        <template x-for="session in allSessions" :key="session.sessionId">
            <a
                :href="'/exposures/' + session.exposureId + '?from=sessions&session_id=' + session.sessionId"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-emerald-300"
            >
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                            :class="session.hasReflection ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                            x-text="session.hasReflection ? '振り返り済み' : '振り返り待ち'"
                        ></span>
                        <span class="text-xs text-gray-400" x-text="formatDate(session.createdAt)"></span>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">回避していること</span>
                        <p class="text-gray-700 text-sm line-clamp-1" x-text="session.avoidanceTarget"></p>
                    </div>
                    <div x-show="session.hierarchyItemContent" class="mb-2">
                        <span class="text-xs text-emerald-600">不安階層表</span>
                        <p class="text-gray-800 text-sm" x-text="session.hierarchyItemContent"></p>
                    </div>
                    <div x-show="session.sudsBefore !== null || session.sudsAfter !== null" class="mb-2">
                        <span class="text-xs text-gray-500">不安レベル</span>
                        <p class="text-sm font-medium text-gray-800">
                            <span x-text="session.sudsBefore ?? '-'"></span>
                            →
                            <span x-text="session.sudsPeak ?? '-'"></span>
                            →
                            <span x-text="session.sudsAfter ?? '-'"></span>
                        </p>
                    </div>
                    <p class="text-gray-800 text-sm line-clamp-2 whitespace-pre-wrap" x-text="session.actionPlan || '未入力'"></p>
                </div>
                <div class="h-1" :class="session.hasReflection ? 'bg-gradient-to-r from-green-400 to-emerald-500' : 'bg-gradient-to-r from-yellow-400 to-orange-400'"></div>
            </a>
        </template>

        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-gray-600 text-lg mt-2">読み込み中...</p>
        </div>

        <div x-show="!loading && allSessions.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-gray-600 text-lg mb-2">まだ実施記録がありません</p>
            <a href="/exposures" class="inline-block mt-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-2 px-6 rounded-lg font-medium">
                エクスポージャーを始める
            </a>
        </div>

        <x-pagination theme-color-from="emerald-500" theme-color-to="teal-500" theme-border-color="emerald-500" />
    </div>

    <a href="/exposures" class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg flex items-center justify-center text-2xl">＋</a>
</div>

<style>
.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<script>
function exposureSessionsListApp() {
    return {
        allSessions: [],
        loading: true,
        filter: new URLSearchParams(window.location.search).get('filter') || 'all',
        keyword: '',
        currentPage: 1,
        perPage: 10,
        lastPage: 1,

        async init() {
            await this.loadSessions();
        },

        async loadSessions() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.keyword) params.append('keyword', this.keyword);
            if (this.filter && this.filter !== 'all') params.append('filter', this.filter);
            params.append('page', this.currentPage);
            params.append('per_page', this.perPage);

            const res = await apiFetch('/api/exposures/sessions?' + params.toString());
            const result = await res.json();

            this.allSessions = (result.data || []).map(s => ({
                sessionId: s.id,
                exposureId: s.exposure_id,
                avoidanceTarget: s.avoidance_target,
                hierarchyItemContent: s.hierarchy_item_content,
                actionPlan: s.action_plan,
                sudsBefore: s.suds_before,
                sudsPeak: s.suds_peak,
                sudsAfter: s.suds_after,
                reflection: s.reflection,
                hasReflection: s.reflection && s.reflection.trim() !== '',
                createdAt: s.created_at
            }));

            this.lastPage = result.last_page || 1;
            this.loading = false;
        },

        async search() {
            this.currentPage = 1;
            await this.loadSessions();
        },

        setFilter(filter) {
            this.filter = filter;
            this.search();
        },

        async clearSearch() {
            this.keyword = '';
            this.currentPage = 1;
            await this.loadSessions();
        },

        async goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) return;
            this.currentPage = page;
            await this.loadSessions();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        get visiblePages() {
            return calculateVisiblePages(this.currentPage, this.lastPage);
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('ja-JP', { year: 'numeric', month: 'long', day: 'numeric' });
        }
    };
}
</script>
@endsection

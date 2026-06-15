@extends('layouts.app')

@section('title', '実施記録一覧')
@section('page-title', '実施記録一覧')

@section('content')
<div x-data="exposureSessionsListApp()" x-init="init()" x-cloak>
    <div class="bg-white rounded-xl shadow-md p-4 mb-4 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">回避していること</label>
            <select x-model="exposureId" @change="onExposureChange()"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-base bg-white">
                <option value="">すべて</option>
                <template x-for="exposure in exposures" :key="exposure.id">
                    <option :value="exposure.id" x-text="exposure.avoidance_target"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">不安階層表</label>
            <select x-model="hierarchyItemId" @change="search()" :disabled="!exposureId"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-base"
                :class="exposureId ? 'bg-white' : 'bg-gray-100 cursor-not-allowed'">
                <option value="">すべて</option>
                <template x-for="item in hierarchyItems" :key="item.id">
                    <option :value="item.id" x-text="item.content"></option>
                </template>
            </select>
            <p x-show="!exposureId" class="text-xs text-gray-500 mt-1">「回避していること」を選択すると絞り込めます</p>
        </div>
        <div class="pt-2 border-t border-gray-200 flex items-center gap-3">
            <button @click="search()" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg text-sm font-medium">
                絞り込む
            </button>
            <button x-show="exposureId || hierarchyItemId" @click="clearFilters()" class="text-sm text-gray-500 hover:text-gray-700 underline">
                絞り込みをクリア
            </button>
        </div>
    </div>

    <div class="space-y-3">
        <template x-for="session in allSessions" :key="session.sessionId">
            <a
                :href="'/exposures/sessions/' + session.sessionId"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-emerald-300"
            >
                <div class="p-4">
                    <div class="flex items-center justify-end mb-2">
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
                    <div x-show="session.sudsAfter !== null" class="mb-2">
                        <span class="text-xs text-gray-500">実施後の不安レベル</span>
                        <p class="text-sm font-medium text-gray-800" x-text="session.sudsAfter"></p>
                    </div>
                    <div x-show="session.reflection">
                        <span class="text-xs text-gray-500">振り返り</span>
                        <p class="text-gray-800 text-sm line-clamp-2 whitespace-pre-wrap" x-text="session.reflection"></p>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-1"></div>
            </a>
        </template>

        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-gray-600 text-lg mt-2">読み込み中...</p>
        </div>

        <div x-show="!loading && allSessions.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-gray-600 text-lg mb-2">まだ実施記録がありません</p>
            <a href="/exposures/sessions/new" class="inline-block mt-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-2 px-6 rounded-lg font-medium">
                実施記録を作成する
            </a>
        </div>

        <x-pagination theme-color-from="emerald-500" theme-color-to="teal-500" theme-border-color="emerald-500" />
    </div>

    <a href="/exposures/sessions/new" class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg flex items-center justify-center text-2xl" title="実施記録を作成">＋</a>
</div>

<style>
.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<script>
function exposureSessionsListApp() {
    return {
        allSessions: [],
        exposures: [],
        hierarchyItems: [],
        exposureId: '',
        hierarchyItemId: '',
        loading: true,
        currentPage: 1,
        perPage: 10,
        lastPage: 1,

        async init() {
            await this.loadExposures();
            await this.loadSessions();
        },

        async loadExposures() {
            this.exposures = await fetchExposureOptions();
        },

        async onExposureChange() {
            this.hierarchyItemId = '';
            this.hierarchyItems = [];
            if (!this.exposureId) {
                await this.search();
                return;
            }
            const res = await apiFetch(`/api/exposures/${this.exposureId}`);
            if (res.ok) {
                const exposure = await res.json();
                this.hierarchyItems = exposure.hierarchy_items || [];
            }
            await this.search();
        },

        async loadSessions() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.exposureId) params.append('exposure_id', this.exposureId);
            if (this.hierarchyItemId) params.append('hierarchy_item_id', this.hierarchyItemId);
            params.append('page', this.currentPage);
            params.append('per_page', this.perPage);

            const res = await apiFetch('/api/exposures/sessions?' + params.toString());
            if (!res.ok) {
                this.allSessions = [];
                this.loading = false;
                return;
            }
            const result = await res.json();

            this.allSessions = (result.data || []).map(s => ({
                sessionId: s.id,
                exposureId: s.exposure_id,
                avoidanceTarget: s.avoidance_target,
                hierarchyItemContent: s.hierarchy_item_content,
                sudsAfter: s.suds_after,
                reflection: s.reflection,
                createdAt: s.created_at
            }));

            this.lastPage = result.last_page || 1;
            this.loading = false;
        },

        async search() {
            this.currentPage = 1;
            await this.loadSessions();
        },

        async clearFilters() {
            this.exposureId = '';
            this.hierarchyItemId = '';
            this.hierarchyItems = [];
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

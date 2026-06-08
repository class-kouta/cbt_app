@extends('layouts.app')

@section('title', 'エクスポージャー療法一覧')
@section('page-title', 'エクスポージャー療法')

@section('content')
<div x-data="exposureListApp()" x-init="init()" x-cloak>
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">キーワード検索</label>
            <div class="flex gap-2">
                <input
                    type="text"
                    x-model="keyword"
                    @keyup.enter="search()"
                    placeholder="回避していること、声かけなどで検索..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base"
                >
                <button
                    @click="search()"
                    class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:from-emerald-600 hover:to-teal-600 transition-all text-sm font-medium"
                >
                    検索
                </button>
            </div>
        </div>

        <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center">
            <div x-show="keyword">
                <button @click="clearSearch()" class="text-sm text-gray-500 hover:text-gray-700 underline">
                    検索条件をクリア
                </button>
            </div>
            <div x-show="!keyword"></div>
            <button
                @click="exportCsv()"
                :disabled="exporting || loading"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-emerald-400 transition-all text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span x-text="exporting ? 'エクスポート中...' : 'CSV出力'"></span>
            </button>
        </div>
    </div>

    <div class="space-y-3">
        <template x-for="item in items" :key="item.id">
            <a
                :href="'/exposures/' + item.id + '?from=list'"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-emerald-300"
            >
                <div class="p-4">
                    <div class="text-xs text-emerald-500 font-medium mb-2" x-text="formatDate(item.created_at)"></div>
                    <p class="text-gray-800 line-clamp-2 break-words overflow-wrap-anywhere mb-2" x-text="item.avoidance_target"></p>
                    <div class="flex items-center gap-1">
                        <span class="text-xs text-gray-500">実施記録 :</span>
                        <span
                            class="inline-block px-2 py-0.5 rounded text-xs"
                            :class="hasSession(item) ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700'"
                            x-text="hasSession(item) ? 'あり' : '未作成'"
                        ></span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-1"></div>
            </a>
        </template>

        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-gray-600 text-lg mt-2">読み込み中...</p>
        </div>

        <div x-show="!loading && items.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-gray-600 text-lg mb-2">まだエクスポージャーの記録がありません</p>
            <a href="/exposures" class="inline-block mt-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-2 px-6 rounded-lg font-medium hover:from-emerald-600 hover:to-teal-600 transition-all">
                エクスポージャーを始める
            </a>
        </div>

        <x-pagination theme-color-from="emerald-500" theme-color-to="teal-500" theme-border-color="emerald-500" />
    </div>

    <a
        href="/exposures"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-emerald-600 hover:to-teal-600 transition-all"
        title="新しいエクスポージャーを作成"
    >＋</a>
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
function exposureListApp() {
    return {
        items: [],
        loading: true,
        exporting: false,
        keyword: '',
        currentPage: 1,
        perPage: 10,
        total: 0,
        lastPage: 1,
        from: 0,
        to: 0,

        async init() {
            await this.loadItems();
        },

        async loadItems() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.keyword) params.append('keyword', this.keyword);
            params.append('page', this.currentPage);
            params.append('per_page', this.perPage);

            const res = await apiFetch('/api/exposures?' + params.toString());
            const result = await res.json();
            this.items = result.data || [];
            this.total = result.total || 0;
            this.currentPage = result.current_page || 1;
            this.lastPage = result.last_page || 1;
            this.from = result.from || 0;
            this.to = result.to || 0;
            this.perPage = result.per_page || 10;
            this.loading = false;
        },

        async search() {
            this.currentPage = 1;
            await this.loadItems();
        },

        async clearSearch() {
            this.keyword = '';
            this.currentPage = 1;
            await this.loadItems();
        },

        async goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) return;
            this.currentPage = page;
            await this.loadItems();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        get visiblePages() {
            return calculateVisiblePages(this.currentPage, this.lastPage);
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('ja-JP', { year: 'numeric', month: 'long', day: 'numeric' });
        },

        hasSession(item) {
            return item.sessions && item.sessions.some(s => s.action_plan && s.action_plan.trim() !== '');
        },

        async exportCsv() {
            this.exporting = true;
            try {
                await exportCsvFromApi('/api/exposures/export/csv', { keyword: this.keyword }, 'exposures.csv', 'エクスポージャー療法');
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

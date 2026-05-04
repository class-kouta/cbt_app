@extends('layouts.app')

@section('title', '問題解決法一覧')
@section('page-title', '問題解決法')

@section('content')
<div x-data="problemSolvingListApp()" x-init="init()" x-cloak>
    <!-- 検索フォーム -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <!-- キーワード検索 -->
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">キーワード検索</label>
            <div class="flex gap-2">
                <input
                    type="text"
                    x-model="keyword"
                    @keyup.enter="search()"
                    placeholder="問題状況、改善イメージで検索..."
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
        
        <!-- タグ検索 -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">タグで絞り込み</label>
            <div class="flex flex-wrap gap-2">
                <template x-for="tag in allTags" :key="tag.id">
                    <button
                        @click="toggleTag(tag.id)"
                        :class="selectedTagIds.includes(tag.id) 
                            ? 'bg-emerald-500 text-white border-emerald-500' 
                            : 'bg-white text-gray-700 border-gray-300 hover:border-emerald-400'"
                        class="px-3 py-1 text-sm rounded-full border transition-all"
                        x-text="tag.name"
                    ></button>
                </template>
            </div>
        </div>
        
        <!-- 検索条件クリア & CSV出力 -->
        <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center">
            <div x-show="keyword || selectedTagIds.length > 0">
                <button
                    @click="clearSearch()"
                    class="text-sm text-gray-500 hover:text-gray-700 underline"
                >
                    検索条件をクリア
                </button>
            </div>
            <div x-show="!(keyword || selectedTagIds.length > 0)"></div>
            <button
                @click="exportCsv()"
                :disabled="exporting || loading"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-emerald-400 transition-all text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
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
    </div>

    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="item in items" :key="item.id">
            <a
                :href="'/problem-solvings/' + item.id + '?from=list'"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-emerald-300"
            >
                <div class="p-4">
                    <!-- 日付 -->
                    <div class="text-xs text-emerald-500 font-medium mb-2" x-text="formatDate(item.created_at)"></div>
                    <!-- 問題状況 -->
                    <p class="text-gray-800 line-clamp-2 break-words overflow-wrap-anywhere mb-2" x-text="item.problem_situation"></p>
                    <!-- タグ表示 -->
                    <div x-show="item.tags && item.tags.length > 0" class="flex flex-wrap gap-1 mb-2">
                        <template x-for="tag in item.tags" :key="tag.id">
                            <span class="inline-block px-2 py-0.5 rounded text-xs bg-sky-100 text-sky-700" x-text="tag.name"></span>
                        </template>
                    </div>
                    <!-- 実行計画ステータス -->
                    <div x-data="{ hasPlan: hasActionPlan(item) }" class="flex items-center gap-1">
                        <span class="text-xs text-gray-500">実行計画 :</span>
                        <span class="inline-block px-2 py-0.5 rounded text-xs" :class="hasPlan ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700'" x-text="hasPlan ? '策定済' : '未策定'"></span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-1"></div>
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
        <div x-show="!loading && items.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">🧩</p>
            <p class="text-gray-600 text-lg mb-2">まだ問題解決の記録がありません</p>
            <a href="/problem-solvings" class="inline-block mt-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-2 px-6 rounded-lg font-medium hover:from-emerald-600 hover:to-teal-600 transition-all">
                問題解決を始める
            </a>
        </div>

        <x-pagination
            theme-color-from="emerald-500"
            theme-color-to="teal-500"
            theme-border-color="emerald-500"
        />
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
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
function problemSolvingListApp() {
    return {
        items: [],
        loading: true,
        exporting: false,
        allTags: [],
        keyword: '',
        selectedTagIds: [],
        // ページネーション用
        currentPage: 1,
        perPage: 10,
        total: 0,
        lastPage: 1,
        from: 0,
        to: 0,

        async init() {
            await Promise.all([
                this.loadTags(),
                this.loadItems()
            ]);
        },

        async loadTags() {
            const res = await apiFetch('/api/tags');
            this.allTags = await res.json();
        },

        async loadItems() {
            this.loading = true;
            
            // クエリパラメータを構築
            const params = new URLSearchParams();
            if (this.keyword) {
                params.append('keyword', this.keyword);
            }
            this.selectedTagIds.forEach(id => {
                params.append('tag_ids[]', id);
            });
            params.append('page', this.currentPage);
            params.append('per_page', this.perPage);
            
            const url = '/api/problem-solvings' + (params.toString() ? '?' + params.toString() : '');
            const res = await apiFetch(url);
            const result = await res.json();
            
            // ページネーション情報を更新
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
            this.currentPage = 1; // 検索時は1ページ目に戻る
            await this.loadItems();
        },

        toggleTag(tagId) {
            const index = this.selectedTagIds.indexOf(tagId);
            if (index === -1) {
                this.selectedTagIds.push(tagId);
            } else {
                this.selectedTagIds.splice(index, 1);
            }
            this.search();
        },

        async clearSearch() {
            this.keyword = '';
            this.selectedTagIds = [];
            this.currentPage = 1;
            await this.loadItems();
        },

        async goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) return;
            this.currentPage = page;
            await this.loadItems();
            // ページ上部にスクロール
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        get visiblePages() {
            return calculateVisiblePages(this.currentPage, this.lastPage);
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        hasActionPlan(item) {
            // plansの中に1つでもaction_planが入力されているものがあるか
            if (!item.plans || item.plans.length === 0) {
                return false;
            }
            return item.plans.some(plan => plan.action_plan && plan.action_plan.trim() !== '');
        },

        async exportCsv() {
            this.exporting = true;
            
            try {
                await exportCsvFromApi(
                    '/api/problem-solvings/export/csv',
                    { keyword: this.keyword, tagIds: this.selectedTagIds },
                    'problem_solvings.csv',
                    '問題解決法'
                );
            } catch (error) {
                console.error('CSV export failed:', error);
                alert('CSVエクスポートに失敗しました');
            } finally {
                this.exporting = false;
            }
        }
    };
}
</script>
@endsection

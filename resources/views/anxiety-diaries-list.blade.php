@extends('layouts.app')

@section('title', '不安日記一覧')
@section('page-title', '不安日記')

@section('content')
<div x-data="anxietyDiaryListApp()" x-init="init()" x-cloak>
    <!-- 検索フォーム -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <!-- キーワード検索 -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">キーワード検索</label>
            <div class="flex gap-2">
                <input
                    type="text"
                    x-model="keyword"
                    @keyup.enter="search()"
                    placeholder="状況、不安、結果などで検索..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent text-base"
                >
                <button
                    @click="search()"
                    class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all text-sm font-medium"
                >
                    検索
                </button>
            </div>
        </div>
        
        <!-- 検索条件クリア -->
        <div x-show="keyword" class="mt-3 pt-3 border-t border-gray-200">
            <button
                @click="clearSearch()"
                class="text-sm text-gray-500 hover:text-gray-700 underline"
            >
                検索条件をクリア
            </button>
        </div>
    </div>

    <!-- 一覧 -->
    <div class="space-y-3">
        <template x-for="item in items" :key="item.id">
            <a
                :href="'/anxiety-diaries/' + item.id"
                class="block bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100 hover:border-amber-300"
            >
                <div class="p-4">
                    <!-- 日付 -->
                    <div class="text-xs text-amber-600 font-medium mb-2" x-text="formatDate(item.created_at)"></div>
                    <!-- 状況 -->
                    <p class="text-gray-800 line-clamp-2 break-words overflow-wrap-anywhere mb-2" x-text="item.situation"></p>
                    <!-- 「実際にどうなったか」のステータス -->
                    <div class="flex items-center gap-1">
                        <span class="text-xs text-gray-500">結果 :</span>
                        <span class="inline-block px-2 py-0.5 rounded text-xs" :class="hasActualOutcome(item) ? 'bg-green-100 text-green-700' : 'bg-sky-100 text-sky-700'" x-text="hasActualOutcome(item) ? '入力済' : '未入力'"></span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-1"></div>
            </a>
        </template>

        <!-- ローディング中 -->
        <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
            <div class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-8 w-8 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600 text-lg">読み込み中...</p>
            </div>
        </div>

        <!-- 空の状態 -->
        <div x-show="!loading && items.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md">
            <p class="text-6xl mb-4">📝</p>
            <p class="text-gray-600 text-lg mb-2">まだ不安日記がありません</p>
            <a href="/anxiety-diaries" class="inline-block mt-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-2 px-6 rounded-lg font-medium hover:from-amber-600 hover:to-orange-600 transition-all">
                不安日記を作成する
            </a>
        </div>

        <x-pagination
            theme-color-from="amber-500"
            theme-color-to="orange-500"
            theme-border-color="amber-500"
        />
    </div>

    <!-- 新規作成ボタン（フローティング） -->
    <a
        href="/anxiety-diaries"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-amber-600 hover:to-orange-600 transition-all"
        title="新しい不安日記を作成"
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
function anxietyDiaryListApp() {
    return {
        items: [],
        loading: true,
        keyword: '',
        // ページネーション用
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
            
            // クエリパラメータを構築
            const params = new URLSearchParams();
            if (this.keyword) {
                params.append('keyword', this.keyword);
            }
            params.append('page', this.currentPage);
            params.append('per_page', this.perPage);
            
            const url = '/api/anxiety-diaries' + (params.toString() ? '?' + params.toString() : '');
            const res = await fetch(url);
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

        async clearSearch() {
            this.keyword = '';
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
            const pages = [];
            const maxVisible = 5;
            
            // 表示開始ページを計算
            let start;
            if (this.lastPage <= maxVisible) {
                // 全ページ数が5以下の場合は1から表示
                start = 1;
            } else if (this.currentPage <= 3) {
                // 現在ページが最初の方なら1から表示
                start = 1;
            } else if (this.currentPage >= this.lastPage - 2) {
                // 現在ページが最後の方なら最後の5ページを表示
                start = this.lastPage - maxVisible + 1;
            } else {
                // それ以外は現在ページを中心に表示
                start = this.currentPage - 2;
            }
            
            // ページ番号を追加（最大5つ）
            for (let i = start; i <= Math.min(start + maxVisible - 1, this.lastPage); i++) {
                pages.push(i);
            }
            
            return pages;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        hasActualOutcome(item) {
            return item.actual_outcome && item.actual_outcome.trim() !== '';
        }
    };
}
</script>
@endsection

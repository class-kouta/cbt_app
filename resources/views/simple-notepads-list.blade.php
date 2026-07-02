@extends('layouts.app')

@section('title', 'メモ帳 - 一覧')
@section('page-title', 'メモ帳')

@section('content')
<div x-data="simpleNotepadListApp()" x-init="init()" x-cloak>

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
                    placeholder="内容で検索..."
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
                            ? getSimpleNotepadTagColor(tag.id).selectedBg + ' text-white ' + getSimpleNotepadTagColor(tag.id).selectedBorder
                            : getSimpleNotepadTagColor(tag.id).bg + ' ' + getSimpleNotepadTagColor(tag.id).text + ' ' + getSimpleNotepadTagColor(tag.id).border + ' ' + getSimpleNotepadTagColor(tag.id).hover"
                        class="px-3 py-1 text-sm rounded-full border transition-all"
                        x-text="tag.name"
                    ></button>
                </template>
            </div>
        </div>

        <!-- 検索条件クリア -->
        <div class="mt-3 pt-3 border-t border-gray-200" x-show="keyword || selectedTagIds.length > 0">
            <button
                @click="clearSearch()"
                class="text-sm text-gray-500 hover:text-gray-700 underline"
            >
                検索条件をクリア
            </button>
        </div>
    </div>

    <!-- メモ帳一覧 -->
    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2" x-show="!loading && total > 0">
            合計: <span x-text="total" class="font-bold"></span> 件
        </div>

        <template x-for="item in simpleNotepads" :key="item.id">
            <a :href="'/simple-notepads/' + item.id + '/edit'" class="block">
                <div class="bg-white rounded-lg shadow-md p-4 transition-all hover:shadow-lg hover:bg-emerald-50 cursor-pointer">
                    <p class="text-gray-600 line-clamp-2 text-sm" x-text="item.content"></p>
                    <!-- タグ表示 -->
                    <div x-show="item.tags && item.tags.length > 0" class="flex flex-wrap gap-1 mt-2">
                        <template x-for="tag in item.tags" :key="tag.id">
                            <span
                                class="inline-block px-2 py-0.5 rounded text-xs"
                                :class="getSimpleNotepadTagColor(tag.id).bg + ' ' + getSimpleNotepadTagColor(tag.id).text"
                                x-text="tag.name"
                            ></span>
                        </template>
                    </div>
                    <div class="mt-2">
                        <span class="text-xs text-gray-400" x-text="formatDate(item.created_at)"></span>
                    </div>
                </div>
            </a>
        </template>

        <!-- ローディング -->
        <div x-show="loading" class="text-center py-16">
            <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 mt-2">読み込み中...</p>
        </div>

        <!-- 空の状態 -->
        <div x-show="!loading && simpleNotepads.length === 0" class="text-center py-12 text-gray-500">
            <div class="mb-4 flex justify-center text-gray-300"><x-icon name="document-text" class="w-12 h-12" /></div>
            <p x-text="keyword || selectedTagIds.length > 0 ? '条件に一致するメモがありません' : 'まだメモがありません'"></p>
            <a href="/simple-notepads" class="text-emerald-600 hover:text-emerald-800 text-sm mt-4 inline-block" x-show="!keyword && selectedTagIds.length === 0">
                メモを書いてみましょう →
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
        href="/simple-notepads"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-emerald-600 hover:to-teal-600 transition-all"
        title="新しいメモを作成"
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
function simpleNotepadListApp() {
    return {
        simpleNotepads: [],
        loading: true,
        allTags: [],
        keyword: '',
        selectedTagIds: [],
        currentPage: 1,
        perPage: 10,
        total: 0,
        lastPage: 1,
        from: 0,
        to: 0,

        async init() {
            await Promise.all([
                this.loadTags(),
                this.loadSimpleNotepads()
            ]);
        },

        async loadTags() {
            try {
                const res = await apiFetch('/api/simple-notepad-tags');
                this.allTags = await res.json();
            } catch (error) {
                console.error(error);
            }
        },

        async loadSimpleNotepads() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.keyword) {
                    params.append('keyword', this.keyword);
                }
                this.selectedTagIds.forEach(id => {
                    params.append('tag_ids[]', id);
                });
                params.append('page', this.currentPage);
                params.append('per_page', this.perPage);

                const url = '/api/simple-notepads' + (params.toString() ? '?' + params.toString() : '');
                const res = await apiFetch(url);
                const result = await res.json();

                this.simpleNotepads = result.data || [];
                this.total = result.total || 0;
                this.currentPage = result.current_page || 1;
                this.lastPage = result.last_page || 1;
                this.from = result.from || 0;
                this.to = result.to || 0;
                this.perPage = result.per_page || 10;
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async search() {
            this.currentPage = 1;
            await this.loadSimpleNotepads();
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

        getSimpleNotepadTagColor(tagId) {
            return getSimpleNotepadTagColor(tagId);
        },

        async clearSearch() {
            this.keyword = '';
            this.selectedTagIds = [];
            this.currentPage = 1;
            await this.loadSimpleNotepads();
        },

        async goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) return;
            this.currentPage = page;
            await this.loadSimpleNotepads();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        get visiblePages() {
            return calculateVisiblePages(this.currentPage, this.lastPage);
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    };
}
</script>
@endsection

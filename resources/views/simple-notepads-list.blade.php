@extends('layouts.app')

@section('title', 'メモ帳 - 一覧')
@section('page-title', 'メモ帳')

@section('content')
<div x-data="simpleNotepadListApp()" x-init="init()" x-cloak>

    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <!-- メモ帳一覧 -->
    <div x-show="!loading" class="space-y-3">
        <div class="text-sm text-gray-600 mb-2" x-show="simpleNotepads.length > 0">
            合計: <span x-text="simpleNotepads.length" class="font-bold"></span> 件
        </div>

        <template x-for="item in simpleNotepads" :key="item.id">
            <a :href="'/simple-notepads/' + item.id + '/edit'" class="block">
                <div class="bg-white rounded-lg shadow-md p-4 transition-all hover:shadow-lg hover:bg-emerald-50 cursor-pointer">
                    <p class="text-gray-900 font-semibold text-sm mb-1 line-clamp-1" x-show="item.title" x-text="item.title"></p>
                    <p class="text-gray-600 line-clamp-2 text-sm" x-text="item.content"></p>
                    <div class="mt-2">
                        <span class="text-xs text-gray-400" x-text="formatDate(item.created_at)"></span>
                    </div>
                </div>
            </a>
        </template>

        <!-- 空の状態 -->
        <div x-show="simpleNotepads.length === 0" class="text-center py-12 text-gray-500">
            <p class="text-4xl mb-4">📝</p>
            <p>まだメモがありません</p>
            <a href="/simple-notepads" class="text-emerald-600 hover:text-emerald-800 text-sm mt-4 inline-block">
                メモを書いてみましょう →
            </a>
        </div>
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

        async init() {
            await this.loadSimpleNotepads();
        },

        async loadSimpleNotepads() {
            this.loading = true;
            try {
                const res = await fetch('/api/simple-notepads');
                this.simpleNotepads = await res.json();
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
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

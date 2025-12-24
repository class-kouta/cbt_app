@extends('layouts.app')

@section('title', 'メモ帳 - 詳細')
@section('page-title', 'メモ帳')

@section('content')
<div x-data="simpleNotepadDetailApp()" x-init="init()" x-cloak>
    <!-- ヘッダー -->
    <div class="flex items-center justify-between mb-6">
        <a href="/simple-notepads/list" class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors flex items-center gap-1">
            ← 一覧に戻る
        </a>
        <div class="flex gap-3" x-show="item">
            <a
                :href="'/simple-notepads/' + item?.id + '/edit'"
                class="text-emerald-600 hover:text-emerald-800 transition-colors p-2 rounded hover:bg-emerald-50"
                title="編集する"
            >
                ✏️
            </a>
            <button
                @click="deleteItem()"
                class="text-xl hover:opacity-70 transition-opacity"
                title="削除"
            >
                🗑️
            </button>
        </div>
    </div>

    <!-- ローディング -->
    <div x-show="loading" class="text-center py-12 text-gray-500">
        <p>読み込み中...</p>
    </div>

    <!-- エラー -->
    <div x-show="error && !loading" class="text-center py-12 text-gray-500">
        <p class="text-4xl mb-4">😢</p>
        <p x-text="error"></p>
        <a href="/simple-notepads/list" class="text-emerald-600 hover:text-emerald-800 text-sm mt-4 inline-block">
            一覧に戻る →
        </a>
    </div>

    <!-- 詳細表示 -->
    <div x-show="item && !loading && !error">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-800 whitespace-pre-wrap break-words" x-text="item?.content"></p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-xs text-gray-400" x-text="item ? formatDate(item.created_at) : ''"></span>
            </div>
        </div>
    </div>
</div>

<script>
function simpleNotepadDetailApp() {
    return {
        item: null,
        loading: true,
        error: '',

        async init() {
            await this.loadItem();
        },

        async loadItem() {
            this.loading = true;
            this.error = '';

            try {
                const id = {{ $itemId }};
                const res = await fetch('/api/simple-notepads');
                const items = await res.json();

                this.item = items.find(item => item.id === id);

                if (!this.item) {
                    this.error = 'メモが見つかりませんでした';
                }
            } catch (e) {
                this.error = 'データの読み込みに失敗しました';
                console.error(e);
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
        },

        async deleteItem() {
            if (!confirm('このメモを削除しますか？')) return;

            try {
                await fetch(`/api/simple-notepads/${this.item.id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                window.location.href = '/simple-notepads/list';
            } catch (e) {
                console.error(e);
                alert('削除に失敗しました');
            }
        }
    };
}
</script>
@endsection

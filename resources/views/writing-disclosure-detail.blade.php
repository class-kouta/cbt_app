@extends('layouts.app')

@section('title', '筆記開示 - 詳細')
@section('page-title', '筆記開示')

@section('content')
<div x-data="writingDisclosureDetailApp()" x-init="init()" x-cloak>
    <!-- ヘッダー -->
    <div class="flex items-center justify-between mb-6">
        <a href="/writing-disclosures/list" class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors flex items-center gap-1">
            ← 一覧に戻る
        </a>
        <div class="flex gap-3" x-show="item">
            <a
                :href="'/writing-disclosures/' + item?.id + '/edit'"
                class="text-xl hover:opacity-70 transition-opacity"
                title="編集"
            >
                <x-icon name="pencil-square" class="w-5 h-5" />
            </a>
            <button
                @click="deleteItem()"
                class="text-xl hover:opacity-70 transition-opacity"
                title="削除"
            >
                <x-icon name="trash" class="w-5 h-5" />
            </button>
        </div>
    </div>

    <!-- ローディング -->
    <div x-show="loading" class="text-center py-12 text-gray-500">
        <p>読み込み中...</p>
    </div>

    <!-- エラー -->
    <div x-show="error && !loading" class="text-center py-12 text-gray-500">
        <div class="mb-4 flex justify-center text-gray-300"><x-icon name="inbox" class="w-12 h-12" /></div>
        <p x-text="error"></p>
        <a href="/writing-disclosures/list" class="text-teal-600 hover:text-teal-800 text-sm mt-4 inline-block">
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
function writingDisclosureDetailApp() {
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
                const res = await apiFetch('/api/writing-disclosures');
                const items = await res.json();

                this.item = items.find(item => item.id === id);

                if (!this.item) {
                    this.error = '記録が見つかりませんでした';
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
            if (!confirm('この記録を削除しますか？')) return;

            try {
                await apiFetch(`/api/writing-disclosures/${this.item.id}`, {
                    method: 'DELETE',
                });

                window.location.href = '/writing-disclosures/list';
            } catch (e) {
                console.error(e);
                alert('削除に失敗しました');
            }
        }
    };
}
</script>
@endsection

@extends('layouts.app')

@section('title', '筆記開示 - 過去の記録')

@section('content')
<div x-data="writingDisclosureListApp()" x-init="init()" x-cloak>
    <!-- ヘッダー -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-800">📋 過去の記録</h1>
        <a href="/writing-disclosures" class="text-orange-600 hover:text-orange-800 text-sm font-medium transition-colors flex items-center gap-1">
            ✏️ 新しく書く →
        </a>
    </div>

    <!-- 筆記開示一覧 -->
    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2">
            合計: <span x-text="writingDisclosures.length" class="font-bold"></span> 件
        </div>

        <template x-for="item in writingDisclosures" :key="item.id">
            <a :href="'/writing-disclosures/' + item.id" class="block">
                <div class="bg-white rounded-lg shadow-md p-4 transition-all hover:shadow-lg hover:bg-orange-50 cursor-pointer">
                    <p class="text-gray-800 line-clamp-2" x-text="item.content"></p>
                    <div class="mt-2">
                        <span class="text-xs text-gray-400" x-text="formatDate(item.created_at)"></span>
                    </div>
                </div>
            </a>
        </template>

        <!-- 空の状態 -->
        <div x-show="writingDisclosures.length === 0" class="text-center py-12 text-gray-500">
            <p class="text-4xl mb-4">📝</p>
            <p>まだ記録がありません</p>
            <a href="/writing-disclosures" class="text-orange-600 hover:text-orange-800 text-sm mt-4 inline-block">
                思いを書き出してみましょう →
            </a>
        </div>
    </div>
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
function writingDisclosureListApp() {
    return {
        writingDisclosures: [],

        async init() {
            await this.loadWritingDisclosures();
        },

        async loadWritingDisclosures() {
            const res = await fetch('/api/writing-disclosures');
            this.writingDisclosures = await res.json();
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

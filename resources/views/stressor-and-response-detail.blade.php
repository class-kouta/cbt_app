@extends('layouts.app')

@section('title', 'ストレッサーとストレス反応詳細')
@section('page-title', 'ストレッサーとストレス反応')

@section('content')
<div x-data="stressorDetailApp()" x-init="init()" x-cloak>
    <!-- コピー成功トースト -->
    <div
        x-show="showCopyToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2"
    >
        <x-icon name="clipboard-document" class="w-5 h-5" />
        <span>コピーしました！</span>
    </div>

    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 mx-auto text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- コンテンツ -->
    <div x-show="!loading && item" class="space-y-4">
        <!-- ヘッダー -->
        <div class="flex items-center justify-between mb-4">
            <a href="/stressor-and-responses/list" class="text-teal-600 hover:text-teal-800 flex items-center gap-1 transition-colors">
                ←
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500" x-text="formatDate(item?.created_at)"></span>
                <!-- 編集ページへのリンク -->
                <a
                    :href="'/stressor-and-responses/' + itemId + '/edit'"
                    class="text-teal-600 hover:text-teal-800 transition-colors p-2 rounded hover:bg-teal-50"
                    title="編集する"
                >
                    <x-icon name="pencil-square" class="w-5 h-5" />
                </a>
                <button
                    @click="deleteItem()"
                    class="text-red-400 hover:text-red-600 transition-colors p-2 rounded hover:bg-red-50"
                    title="削除"
                >
                    <x-icon name="trash" class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- 詳細表示 -->
        <div class="space-y-4">
            <!-- ストレッサー -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4">
                    ストレッサー
                </h3>
                <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" x-text="item?.stressor || '未入力'"></p>
            </div>

            <!-- タグ -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200" x-show="item?.tags && item.tags.length > 0">
                <h3 class="text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <x-icon name="tag" class="w-4 h-4" /> タグ
                </h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="tag in (item?.tags || [])" :key="tag.id">
                        <span class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium" x-text="tag.name"></span>
                    </template>
                </div>
            </div>

            <!-- ストレス反応セクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4">
                    ストレス反応
                </h3>
                
                <div class="space-y-4">
                    <!-- 認知（自動思考） -->
                    <div class="bg-amber-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-amber-600 mb-2">
                            認知（自動思考）
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.cognition ? 'text-gray-400' : ''" x-text="item?.cognition || '未入力'"></p>
                    </div>

                    <!-- 気分・感情 -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-blue-600 mb-2">
                            気分・感情
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.mood ? 'text-gray-400' : ''" x-text="item?.mood || '未入力'"></p>
                    </div>

                    <!-- 身体反応 -->
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-green-600 mb-2">
                            身体反応
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.body_reaction ? 'text-gray-400' : ''" x-text="item?.body_reaction || '未入力'"></p>
                    </div>

                    <!-- 行動 -->
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-purple-600 mb-2">
                            行動
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!item?.behavior ? 'text-gray-400' : ''" x-text="item?.behavior || '未入力'"></p>
                    </div>
                </div>
            </div>

            <!-- コピーボタン -->
            <button
                type="button"
                @click="copyToClipboard()"
                class="w-full bg-white border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all flex items-center justify-center gap-2"
            >
                <x-icon name="clipboard-document" class="w-5 h-5" /> 内容をコピー
            </button>
        </div>
    </div>

    <!-- エラー -->
    <div x-show="!loading && !item" class="text-center py-16 bg-white rounded-xl shadow-md">
        <div class="mb-4 flex justify-center text-gray-300"><x-icon name="inbox" class="w-16 h-16" /></div>
        <p class="text-gray-600 text-lg mb-2">データが見つかりません</p>
        <a href="/stressor-and-responses/list" class="inline-block mt-4 text-teal-600 hover:text-teal-800">
            ←
        </a>
    </div>
</div>

<script>
function stressorDetailApp() {
    return {
        item: null,
        loading: true,
        itemId: {{ $itemId }},
        showCopyToast: false,

        async init() {
            await this.loadItem();
        },

        async loadItem() {
            try {
                const res = await apiFetch(`/api/stressor-and-responses/${this.itemId}`);
                if (res.ok) {
                    this.item = await res.json();
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        async deleteItem() {
            if (!confirm('この記録を削除しますか？')) return;

            try {
                await apiFetch(`/api/stressor-and-responses/${this.itemId}`, {
                    method: 'DELETE',
                });
                window.location.href = '/stressor-and-responses/list';
            } catch (e) {
                console.error(e);
            }
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        generateCopyText() {
            const sections = [];
            sections.push('【ストレッサーとストレス反応】');
            sections.push('');

            sections.push('■ ストレッサー');
            sections.push(this.item?.stressor || '未入力');
            sections.push('');

            sections.push('■ 認知（自動思考）');
            sections.push(this.item?.cognition || '未入力');
            sections.push('');

            sections.push('■ 気分・感情');
            sections.push(this.item?.mood || '未入力');
            sections.push('');

            sections.push('■ 身体反応');
            sections.push(this.item?.body_reaction || '未入力');
            sections.push('');

            sections.push('■ 行動');
            sections.push(this.item?.behavior || '未入力');

            return sections.join('\n').trim();
        },

        async copyToClipboard() {
            const text = this.generateCopyText();
            let copied = false;

            try {
                await navigator.clipboard.writeText(text);
                copied = true;
            } catch (err) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    copied = true;
                } catch (err) {
                    console.error('コピーに失敗しました:', err);
                }
                document.body.removeChild(textArea);
            }

            if (copied) {
                this.showCopyToast = true;
                setTimeout(() => {
                    this.showCopyToast = false;
                }, 2000);
            }
        }
    };
}
</script>
@endsection

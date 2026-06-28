@extends('layouts.app')

@section('title', '問題解決法詳細')
@section('page-title', '問題解決法')

@section('content')
<div x-data="problemSolvingDetailApp({{ $itemId }})" x-init="init()" x-cloak>
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

    <!-- ヘッダー -->
    <div class="flex justify-between items-center mb-4">
        <a href="/problem-solvings/list" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
            ← 一覧に戻る
        </a>
        <div class="flex gap-2">
            <a
                :href="'/problem-solvings/' + itemId + '/edit'"
                class="text-emerald-600 hover:text-emerald-800 transition-colors p-2 rounded hover:bg-emerald-50"
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

    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <!-- 詳細表示 -->
    <div x-show="!loading && item">

        <div class="space-y-6">
            <!-- Step 1: 問題状況 -->
            <div class="bg-emerald-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-emerald-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-xs">1</span>
                    問題状況
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words" x-text="item?.problem_situation"></p>
            </div>

            <!-- タグ -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200" x-show="item?.tags && item.tags.length > 0">
                <div class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-1">
                    <x-icon name="tag" class="w-4 h-4" /> タグ
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="tag in (item?.tags || [])" :key="tag.id">
                        <span class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium" x-text="tag.name"></span>
                    </template>
                </div>
            </div>

            <!-- Step 2: 改善イメージ -->
            <div class="bg-teal-50 rounded-lg p-4">
                <div class="text-xs font-semibold text-teal-600 mb-2 flex items-center gap-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-teal-500 text-white text-xs">2</span>
                    改善イメージ
                </div>
                <p class="text-gray-800 whitespace-pre-wrap break-words" :class="!item?.improved_image ? 'text-gray-400' : ''" x-text="item?.improved_image || '未入力'"></p>
            </div>

            <!-- 実行計画と振り返り（複数対応） -->
            <div class="border-t border-gray-200 pt-4">
                <div class="mb-4">
                    <span class="text-sm font-semibold text-gray-700">実行計画と振り返り</span>
                </div>

                <!-- 計画がない場合 -->
                <div x-show="!item?.plans || item.plans.length === 0" class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-400">まだ計画がありません</p>
                </div>

                <!-- 計画一覧 -->
                <div class="space-y-4">
                    <template x-for="(plan, index) in item?.plans" :key="plan.id">
                        <div class="border rounded-xl overflow-hidden"
                            :class="plan.reflection && plan.reflection.trim()
                                ? 'border-green-200 bg-green-50'
                                : (plan.action_plan && plan.action_plan.trim()
                                    ? 'border-yellow-200 bg-yellow-50'
                                    : 'border-gray-200 bg-gray-50')">
                            <!-- 計画ヘッダー -->
                            <div class="px-4 py-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <!-- ステータスバッジ -->
                                    <span
                                        x-show="plan.reflection && plan.reflection.trim()"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"
                                    >
                                        <x-icon name="check" class="w-3.5 h-3.5 inline-block" /> 振り返り済み
                                    </span>
                                    <span
                                        x-show="plan.action_plan && plan.action_plan.trim() && (!plan.reflection || !plan.reflection.trim())"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700"
                                    >
                                        実行中
                                    </span>
                                    <span
                                        x-show="!plan.action_plan || !plan.action_plan.trim()"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"
                                    >
                                        計画作成中
                                    </span>
                                </div>
                            </div>

                            <!-- 計画コンテンツ -->
                            <div class="px-4 pb-4 space-y-4">
                                <!-- 実行計画 -->
                                <div class="bg-white rounded-lg p-3">
                                    <div class="text-xs font-semibold text-teal-600 mb-2 flex items-center gap-1">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-teal-500 text-white text-xs">3</span>
                                        実行計画
                                    </div>
                                    <p class="text-gray-800 whitespace-pre-wrap break-words" :class="!plan.action_plan ? 'text-gray-400' : ''" x-text="plan.action_plan || '未入力'"></p>
                                </div>

                                <!-- 振り返り -->
                                <div class="bg-white rounded-lg p-3">
                                    <div class="text-xs font-semibold text-lime-600 mb-2 flex items-center justify-between">
                                        <div class="flex items-center gap-1">
                                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-lime-500 text-white text-xs">4</span>
                                            振り返り
                                        </div>
                                        <span
                                            x-show="plan.reflection && plan.reflection.trim()"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold"
                                            :class="{
                                                'bg-gray-100 text-gray-500': !plan.improvement_level,
                                                'bg-red-100 text-red-700': plan.improvement_level && plan.improvement_level <= 3,
                                                'bg-yellow-100 text-yellow-700': plan.improvement_level && plan.improvement_level >= 4 && plan.improvement_level <= 6,
                                                'bg-green-100 text-green-700': plan.improvement_level && plan.improvement_level >= 7
                                            }"
                                        >
                                            <x-icon name="chart-bar" class="w-3.5 h-3.5 inline-block" /> 改善Lv.<span x-text="plan.improvement_level || '-'"></span>
                                        </span>
                                    </div>
                                    <p class="text-gray-800 whitespace-pre-wrap break-words" :class="!plan.reflection ? 'text-gray-400' : ''" x-text="plan.reflection || '未入力'"></p>
                                </div>
                            </div>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- コピーボタン -->
                <button
                    type="button"
                    @click="copyToClipboard()"
                    class="w-full bg-white border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all flex items-center justify-center gap-2 mt-4"
                >
                    <x-icon name="clipboard-document" class="w-5 h-5" /> 内容をコピー
                </button>
        </div>
    </div>
</div>

<script>
function problemSolvingDetailApp(itemId) {
    return {
        itemId: itemId,
        item: null,
        loading: true,
        showCopyToast: false,

        async init() {
            await this.loadItem();
        },

        async loadItem() {
            this.loading = true;
            try {
                const res = await apiFetch(`/api/problem-solvings/${this.itemId}`);
                if (res.ok) {
                    this.item = await res.json();
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        async deleteItem() {
            if (!confirm('この記録を削除しますか？')) return;

            try {
                const res = await apiFetch(`/api/problem-solvings/${this.itemId}`, {
                    method: 'DELETE'
                });

                if (res.ok) {
                    window.location.href = '/problem-solvings/list';
                } else {
                    alert('削除に失敗しました');
                }
            } catch (error) {
                console.error(error);
                alert('削除に失敗しました');
            }
        },

        generateCopyText() {
            const sections = [];
            sections.push('【問題解決法】');
            sections.push('');

            sections.push('■ 問題状況');
            sections.push(this.item?.problem_situation || '未入力');
            sections.push('');

            sections.push('■ 改善イメージ');
            sections.push(this.item?.improved_image || '未入力');
            sections.push('');

            // 計画と振り返り
            if (this.item?.plans && this.item.plans.length > 0) {
                this.item.plans.forEach((plan, index) => {
                    const planLabel = this.item.plans.length > 1 ? `■ 実行計画 ${index + 1}` : '■ 実行計画';
                    sections.push(planLabel);
                    sections.push(plan.action_plan || '未入力');
                    sections.push('');

                    const reflectionLabel = this.item.plans.length > 1 ? `■ 振り返り ${index + 1}` : '■ 振り返り';
                    sections.push(reflectionLabel);
                    sections.push(plan.reflection || '未入力');
                    sections.push('');
                });
            } else {
                sections.push('■ 実行計画');
                sections.push('未入力');
                sections.push('');
                sections.push('■ 振り返り');
                sections.push('未入力');
            }

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

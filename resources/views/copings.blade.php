@extends('layouts.app')

@section('title', 'コーピングリスト')
@section('page-title', 'コーピングリスト')

@section('content')
<div x-data="copingApp()" x-init="init()" x-cloak>
    <!-- 新規コーピング作成フォーム -->
    <div class="mb-6">
        <form @submit.prevent="createCoping()">
            <div class="space-y-4">
                <!-- 内容 -->
                <div>
                    <textarea
                        x-model="newCoping.content"
                        rows="2"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="コーピングを入力..."
                        maxlength="200"
                        required
                    ></textarea>
                    <div class="text-xs text-gray-400 text-right" x-text="newCoping.content.length + '/200'"></div>
                </div>

                <!-- タグ選択 -->
                <div x-show="copingTags.length > 0">
                    <label class="block text-sm font-medium text-gray-700 mb-1">タグ（任意）</label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in copingTags" :key="tag.id">
                            <label class="cursor-pointer">
                                <input
                                    type="checkbox"
                                    :value="tag.id"
                                    x-model.number="newCoping.coping_tag_ids"
                                    class="sr-only"
                                >
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-sm border-2 transition-all"
                                    :class="newCoping.coping_tag_ids.includes(tag.id) ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-gray-600 border-gray-300 hover:border-emerald-300'"
                                    x-text="tag.name"
                                ></span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- エラーメッセージ -->
                <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

                <!-- 送信ボタン -->
                <div>
                    <button
                        type="submit"
                        class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50"
                        :disabled="loading || !newCoping.content.trim()"
                    >
                        <span x-show="!loading">追加</span>
                        <span x-show="loading">追加中...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- フィルター -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6" x-show="copingTags.length > 0">
        <div class="flex flex-wrap gap-2 items-center">
            <span class="text-sm font-medium text-gray-700">絞り込み:</span>
            <button
                @click="filterTagId = null"
                class="px-3 py-1 rounded-full text-sm transition-all"
                :class="filterTagId === null ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
            >
                すべて
            </button>
            <template x-for="tag in copingTags" :key="tag.id">
                <button
                    @click="filterTagId = tag.id"
                    class="px-3 py-1 rounded-full text-sm transition-all"
                    :class="filterTagId === tag.id ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    x-text="tag.name"
                ></button>
            </template>
        </div>
    </div>

    <!-- コーピング一覧 -->
    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2">
            合計: <span x-text="filteredCopings.length" class="font-bold"></span> 件
        </div>

        <template x-for="coping in filteredCopings" :key="coping.id">
            <div class="bg-white rounded-lg shadow-md p-4 transition-all hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <!-- 内容 -->
                    <div class="flex-1 min-w-0">
                        <!-- 編集モード -->
                        <div x-show="editingId === coping.id">
                            <textarea
                                x-model="editContent"
                                x-ref="editTextarea"
                                rows="2"
                                @keydown.escape="cancelEdit()"
                                @keydown.meta.enter="saveEdit(coping)"
                                @keydown.ctrl.enter="saveEdit(coping)"
                                class="w-full border border-emerald-400 rounded-lg px-3 py-2 text-base focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-emerald-50"
                                maxlength="200"
                            ></textarea>
                            <!-- 編集時のタグ選択 -->
                            <div class="flex flex-wrap gap-2 mt-2" x-show="copingTags.length > 0">
                                <template x-for="tag in copingTags" :key="tag.id">
                                    <label class="cursor-pointer">
                                        <input
                                            type="checkbox"
                                            :value="tag.id"
                                            x-model.number="editTagIds"
                                            class="sr-only"
                                        >
                                        <span
                                            class="inline-block px-2 py-0.5 rounded-full text-xs border transition-all"
                                            :class="editTagIds.includes(tag.id) ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-gray-600 border-gray-300'"
                                            x-text="tag.name"
                                        ></span>
                                    </label>
                                </template>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                <button
                                    type="button"
                                    @click="saveEdit(coping)"
                                    class="text-emerald-600 hover:text-emerald-700 transition-colors p-1.5 rounded hover:bg-emerald-50"
                                    title="保存"
                                >
                                    <x-icon name="check" class="w-5 h-5" />
                                </button>
                                <button
                                    type="button"
                                    @click="deleteCoping(coping)"
                                    class="text-red-400 hover:text-red-600 transition-colors p-1.5 rounded hover:bg-red-50"
                                    title="削除"
                                >
                                    <x-icon name="trash" class="w-5 h-5" />
                                </button>
                                <span
                                    @click="cancelEdit()"
                                    class="text-sm text-gray-500 hover:text-gray-700 underline cursor-pointer select-none ml-1"
                                >
                                    キャンセル
                                </span>
                            </div>
                        </div>

                        <!-- 表示モード（クリックで編集） -->
                        <div
                            x-show="editingId !== coping.id"
                            @click="startEdit(coping)"
                            class="cursor-pointer hover:bg-gray-50 rounded-lg p-1 -m-1 transition-colors"
                            title="クリックして編集"
                        >
                            <p class="text-gray-800 break-words overflow-wrap-anywhere" x-text="coping.content"></p>
                            <div class="flex flex-wrap gap-1 mt-2">
                                <template x-for="tag in coping.coping_tags" :key="tag.id">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600" x-text="'#' + tag.name"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </template>

        <!-- 空の状態 -->
        <div x-show="filteredCopings.length === 0" class="text-center py-12 text-gray-500">
            <div class="mb-4 flex justify-center text-gray-300"><x-icon name="sun" class="w-12 h-12" /></div>
            <p>コーピングがありません</p>
        </div>
    </div>
</div>

<script>
function copingApp() {
    return {
        copings: [],
        copingTags: [],
        newCoping: {
            content: '',
            coping_tag_ids: []
        },
        filterTagId: null,
        editingId: null,
        editContent: '',
        editTagIds: [],
        loading: false,
        error: '',

        async init() {
            await Promise.all([
                this.loadCopings(),
                this.loadCopingTags()
            ]);
        },

        async loadCopings() {
            const res = await apiFetch('/api/copings');
            this.copings = await res.json();
        },

        async loadCopingTags() {
            const res = await apiFetch('/api/coping-tags');
            this.copingTags = await res.json();
        },

        get filteredCopings() {
            if (this.filterTagId === null) {
                return this.copings;
            }
            return this.copings.filter(c =>
                c.coping_tags.some(t => t.id === this.filterTagId)
            );
        },

        async createCoping() {
            this.error = '';

            if (!this.newCoping.content.trim()) {
                this.error = 'コーピング内容を入力してください';
                return;
            }

            this.loading = true;
            try {
                const res = await apiFetch('/api/copings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.newCoping)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                this.newCoping = { content: '', coping_tag_ids: [] };
                await this.loadCopings();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        startEdit(coping) {
            this.editingId = coping.id;
            this.editContent = coping.content;
            this.editTagIds = coping.coping_tags.map(t => t.id);
            // 次のティックでテキストエリアにフォーカス
            this.$nextTick(() => {
                const textarea = document.querySelector(`[x-ref="editTextarea"]`);
                if (textarea) {
                    textarea.focus();
                    // カーソルを末尾に移動
                    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                }
            });
        },

        cancelEdit() {
            this.editingId = null;
            this.editContent = '';
            this.editTagIds = [];
        },

        async saveEdit(coping) {
            if (!this.editContent.trim()) {
                alert('コーピング内容を入力してください');
                return;
            }

            try {
                const res = await apiFetch(`/api/copings/${coping.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.editContent,
                        coping_tag_ids: this.editTagIds
                    })
                });

                if (res.ok) {
                    this.cancelEdit();
                    await this.loadCopings();
                }
            } catch (e) {
                console.error(e);
            }
        },

        async deleteCoping(coping) {
            if (!confirm('このコーピングを削除しますか？')) return;

            try {
                await apiFetch(`/api/copings/${coping.id}`, {
                    method: 'DELETE',
                });
                await this.loadCopings();
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection

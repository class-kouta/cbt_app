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

    <!-- コーピング一覧 -->
    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2">
            合計: <span x-text="copings.length" class="font-bold"></span> 件
        </div>

        <template x-for="coping in copings" :key="coping.id">
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
                            <div class="flex items-center gap-2 mt-2">
                                <button
                                    type="button"
                                    @click="saveEdit(coping)"
                                    class="text-emerald-600 hover:text-emerald-700 transition-colors p-1.5 rounded hover:bg-emerald-50"
                                    title="保存"
                                >
                                    <x-icon name="floppy-disk" class="w-5 h-5" />
                                </button>
                                <button
                                    type="button"
                                    @click="deleteCoping(coping)"
                                    class="text-gray-500 hover:text-gray-700 transition-colors p-1.5 rounded hover:bg-gray-50"
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
                        </div>
                    </div>

                </div>
            </div>
        </template>

        <!-- 空の状態 -->
        <div x-show="copings.length === 0" class="text-center py-12 text-gray-500">
            <div class="mb-4 flex justify-center text-gray-300"><x-icon name="sun" class="w-12 h-12" /></div>
            <p>コーピングがありません</p>
        </div>
    </div>
</div>

<script>
function copingApp() {
    return {
        copings: [],
        newCoping: {
            content: ''
        },
        editingId: null,
        editContent: '',
        loading: false,
        error: '',

        async init() {
            await this.loadCopings();
        },

        async loadCopings() {
            const res = await apiFetch('/api/copings');
            this.copings = await res.json();
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

                this.newCoping = { content: '' };
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
            this.$nextTick(() => {
                const textarea = document.querySelector(`[x-ref="editTextarea"]`);
                if (textarea) {
                    textarea.focus();
                    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                }
            });
        },

        cancelEdit() {
            this.editingId = null;
            this.editContent = '';
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
                        content: this.editContent
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

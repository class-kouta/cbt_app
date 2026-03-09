@extends('layouts.app')

@section('title', 'ヘルシーサイドとスキーマサイドの対話のワーク - ココロの避難所')
@section('page-title', '対話のワーク')

@section('content')
<div x-data="dialogueWorkEditApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <!-- 編集モード時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a href="/schema-therapy/dialogue-work" class="text-purple-600 hover:text-purple-800 flex items-center gap-1">
            ← 一覧に戻る
        </a>
        <button
            @click="confirmDelete()"
            class="text-red-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50 flex items-center gap-1 text-sm"
            title="削除"
        >
            🗑️ <span class="hidden sm:inline">削除</span>
        </button>
    </div>

    <!-- 新規作成時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="!isEditMode">
        <a href="/schema-therapy/dialogue-work" class="text-purple-600 hover:text-purple-800 flex items-center gap-1">
            ← 一覧に戻る
        </a>
    </div>

    <!-- ローディング（編集モードのみ） -->
    <div x-show="loading && isEditMode" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-purple-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <!-- 自動保存トースト -->
    <div
        x-show="showAutoSaveToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-16 right-4 bg-orange-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        自動保存しました
    </div>

    <!-- 手動保存トースト -->
    <div
        x-show="showManualSaveToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-16 right-4 bg-green-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        保存しました
    </div>

    <!-- 説明（新規作成時のみ表示） -->
    <div x-show="!isEditMode && !loading" class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
        <p class="text-purple-800 text-sm">
            💬 ヘルシーサイドとスキーマサイドの対話を書き出してみましょう。ボタンを使って話者を切り替えながら、内なる対話を外在化できます。
        </p>
    </div>

    <!-- フォーム -->
    <div x-show="!loading || !isEditMode">
        <form @submit.prevent="saveItem()">
            <div class="space-y-4">
                <div>
                    <textarea
                        x-ref="contentArea"
                        x-model="content"
                        rows="100"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all font-mono text-base leading-relaxed"
                        placeholder="ヘルシーサイドとスキーマサイドの対話を書いてみましょう..."
                        maxlength="50000"
                        required
                    ></textarea>
                    <div class="text-xs text-gray-400 text-right" x-text="content.length + '/50000'"></div>
                </div>

                <!-- エラーメッセージ -->
                <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>
            </div>
        </form>
    </div>

    <!-- 固定フローティングボタン群（右下） -->
    <div class="fixed bottom-6 right-6 flex flex-col items-center gap-3 z-30">
        <!-- ヘルシーサイドボタン -->
        <button
            type="button"
            @click="insertHealthySide()"
            class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-full font-semibold text-sm hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg hover:shadow-xl whitespace-nowrap"
        >
            ヘルシーサイド
        </button>

        <!-- スキーマサイドボタン -->
        <button
            type="button"
            @click="insertSchemaSide()"
            class="px-4 py-2.5 bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full font-semibold text-sm hover:from-rose-600 hover:to-pink-600 transition-all shadow-lg hover:shadow-xl whitespace-nowrap"
        >
            スキーマサイド
        </button>

        <!-- 保存ボタン（丸アイコン） -->
        <button
            type="button"
            @click="manualSave()"
            :disabled="submitting || floatingSaving || !isFormValid()"
            class="w-14 h-14 bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center hover:from-purple-600 hover:to-indigo-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            title="保存する"
        >
            <template x-if="!floatingSaving && !submitting">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zM16 20v-6H8v6M8 4v4h6"></path>
                </svg>
            </template>
            <template x-if="floatingSaving || submitting">
                <svg class="animate-spin w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
        </button>
    </div>

    <!-- 削除確認モーダル -->
    <div
        x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="showDeleteModal = false"
    >
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showDeleteModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl"
                @click.stop
            >
                <div class="bg-gradient-to-r from-red-500 to-rose-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        🗑️ 削除確認
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <p class="text-gray-700 text-base">この対話のワークを削除しますか？</p>
                    <p class="text-sm text-gray-500 mt-2">この操作は取り消せません。</p>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="flex-1 py-2.5 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                    >
                        キャンセル
                    </button>
                    <button
                        type="button"
                        @click="executeDelete()"
                        :disabled="deleting"
                        class="flex-1 py-2.5 px-4 bg-gradient-to-r from-red-500 to-rose-500 text-white rounded-lg font-medium hover:from-red-600 hover:to-rose-600 transition-colors disabled:opacity-50"
                    >
                        <span x-show="!deleting">削除する</span>
                        <span x-show="deleting" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            削除中...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dialogueWorkEditApp(itemId) {
    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        content: '',
        loading: false,
        submitting: false,
        error: '',
        showAutoSaveToast: false,
        showManualSaveToast: false,
        floatingSaving: false,
        showDeleteModal: false,
        deleting: false,

        autoSaveSnapshots: [],
        autoSaveInterval: null,
        autoSaving: false,
        _saveInProgress: false,

        async init() {
            if (this.isEditMode) {
                await this.loadItem();
            }

            this.takeSnapshot();

            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        async loadItem() {
            this.loading = true;
            try {
                const res = await fetch(`/api/dialogue-works/${this.itemId}`);
                if (!res.ok) {
                    throw new Error('Failed to load item.');
                }
                const item = await res.json();

                if (item) {
                    this.content = item.content || '';
                }
            } catch (error) {
                this.error = 'データの読み込みに失敗しました。';
            } finally {
                this.loading = false;
            }
        },

        isFormValid() {
            return this.content.trim().length > 0;
        },

        insertHealthySide() {
            this.content += '\nヘルシーサイド：\n';
            this.scrollToBottom();
        },

        insertSchemaSide() {
            this.content += '\nスキーマサイド：\n';
            this.scrollToBottom();
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const textarea = this.$refs.contentArea;
                if (textarea) {
                    textarea.scrollTop = textarea.scrollHeight;
                    textarea.focus();
                    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                }
            });
        },

        takeSnapshot() {
            this.autoSaveSnapshots.push(this.content);

            if (this.autoSaveSnapshots.length > 2) {
                this.autoSaveSnapshots.shift();
            }
        },

        hasChangedFromPreviousSnapshot() {
            if (this.autoSaveSnapshots.length < 2) {
                if (this.autoSaveSnapshots.length === 1) {
                    return this.content !== this.autoSaveSnapshots[0];
                }
                return false;
            }

            return this.content !== this.autoSaveSnapshots[0];
        },

        async checkAndAutoSave() {
            if (
                this.content.trim() &&
                this.hasChangedFromPreviousSnapshot() &&
                !this.submitting &&
                !this.autoSaving &&
                !this.floatingSaving
            ) {
                await this.performAutoSave();
            }

            this.takeSnapshot();
        },

        async performSave({ isManual = false, redirectOnSuccess = false } = {}) {
            if (this._saveInProgress) {
                return;
            }
            this._saveInProgress = true;
            try {
                const isUpdate = !!this.itemId;
                const url = isUpdate
                    ? `/api/dialogue-works/${this.itemId}`
                    : '/api/dialogue-works';
                const method = isUpdate ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content: this.content })
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                if (!isUpdate) {
                    const data = await res.json();
                    this.itemId = data.id;
                    this.isEditMode = true;
                    history.replaceState(null, '', `/schema-therapy/dialogue-work/${this.itemId}/edit`);
                }

                if (redirectOnSuccess) {
                    window.location.href = '/schema-therapy/dialogue-work';
                    return;
                }

                this.showSaveNotification(isManual);
            } catch (error) {
                if (redirectOnSuccess) {
                    this.error = error.message;
                    this.submitting = false;
                }
            } finally {
                this._saveInProgress = false;
            }
        },

        async performAutoSave() {
            this.autoSaving = true;
            try {
                await this.performSave({ isManual: false });
            } finally {
                this.autoSaving = false;
            }
        },

        async manualSave() {
            if (this.floatingSaving || this.autoSaving || this.submitting || !this.isFormValid()) return;

            this.floatingSaving = true;
            try {
                await this.performSave({ isManual: true });
            } finally {
                this.floatingSaving = false;
            }
        },

        showSaveNotification(isManual = false) {
            if (isManual) {
                this.showManualSaveToast = true;
                setTimeout(() => {
                    this.showManualSaveToast = false;
                }, 2000);
            } else {
                this.showAutoSaveToast = true;
                setTimeout(() => {
                    this.showAutoSaveToast = false;
                }, 2000);
            }
        },

        async saveItem() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = '内容を入力してください';
                return;
            }

            this.submitting = true;

            while (this._saveInProgress) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }

            await this.performSave({ isManual: true, redirectOnSuccess: true });
        },

        confirmDelete() {
            this.showDeleteModal = true;
        },

        async executeDelete() {
            if (!this.itemId || this.deleting) return;

            this.deleting = true;
            this.error = '';
            try {
                const res = await fetch(`/api/dialogue-works/${this.itemId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                if (res.ok || res.status === 204) {
                    if (this.autoSaveInterval) {
                        clearInterval(this.autoSaveInterval);
                    }
                    window.location.href = '/schema-therapy/dialogue-work';
                } else {
                    this.error = '削除中にエラーが発生しました。';
                    this.showDeleteModal = false;
                }
            } catch (error) {
                this.error = '削除中にエラーが発生しました。';
                this.showDeleteModal = false;
            } finally {
                this.deleting = false;
            }
        }
    };
}
</script>
@endsection

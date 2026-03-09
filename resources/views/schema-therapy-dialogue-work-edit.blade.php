@extends('layouts.app')

@section('title', 'ヘルシーサイドとスキーマサイドの対話のワーク - ココロの避難所')
@section('page-title', '対話のワーク')

@section('content')
<div x-data="dialogueWorkEditApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak class="pb-24">
    <!-- ヘッダー -->
    <div class="flex justify-between items-center mb-4">
        <a href="/schema-therapy/dialogue-work" class="text-purple-600 hover:text-purple-800 flex items-center gap-1 text-sm">
            ← 一覧に戻る
        </a>
        <button
            x-show="isEditMode"
            @click="confirmDelete()"
            class="text-red-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50 flex items-center gap-1 text-sm"
            title="削除"
        >
            🗑️ <span class="hidden sm:inline">削除</span>
        </button>
    </div>

    <!-- ローディング -->
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

    <!-- メインコンテンツ -->
    <div x-show="!loading || !isEditMode">
        <!-- 話者追加ボタン -->
        <div class="flex gap-3 mb-4">
            <button
                type="button"
                @click="addEntry('healthy')"
                class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-xl font-semibold text-base hover:from-blue-600 hover:to-cyan-600 transition-all shadow-md hover:shadow-lg"
            >
                ヘルシーサイド
            </button>
            <button
                type="button"
                @click="addEntry('schema')"
                class="flex-1 py-3 bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-xl font-semibold text-base hover:from-rose-600 hover:to-pink-600 transition-all shadow-md hover:shadow-lg"
            >
                スキーマサイド
            </button>
        </div>

        <!-- 対話エントリ一覧 -->
        <div class="space-y-3" x-ref="entriesContainer">
            <template x-for="(entry, index) in entries" :key="entry.id">
                <div
                    class="rounded-xl shadow-sm border overflow-hidden"
                    :class="entry.type === 'healthy'
                        ? 'border-blue-200 bg-blue-50/50'
                        : 'border-rose-200 bg-rose-50/50'"
                >
                    <!-- ラベルヘッダー -->
                    <div
                        class="px-4 py-2 flex items-center justify-between"
                        :class="entry.type === 'healthy'
                            ? 'bg-gradient-to-r from-blue-500 to-cyan-500'
                            : 'bg-gradient-to-r from-rose-500 to-pink-500'"
                    >
                        <span class="text-white font-semibold text-sm" x-text="entry.type === 'healthy' ? 'ヘルシーサイド' : 'スキーマサイド'"></span>
                        <button
                            type="button"
                            @click="removeEntry(index)"
                            class="text-white/70 hover:text-white transition-colors"
                            title="削除"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- テキスト入力 -->
                    <div class="p-3">
                        <textarea
                            x-model="entry.text"
                            rows="4"
                            class="w-full border rounded-lg px-3 py-2 text-base leading-relaxed resize-y focus:ring-2 focus:border-transparent transition-all"
                            :class="entry.type === 'healthy'
                                ? 'border-blue-200 focus:ring-blue-400 bg-white'
                                : 'border-rose-200 focus:ring-rose-400 bg-white'"
                            placeholder="ここに書いてください..."
                            @input="onEntryInput($event, index)"
                        ></textarea>
                    </div>
                </div>
            </template>
        </div>

        <!-- 空の状態 -->
        <div x-show="entries.length === 0" class="text-center py-20 bg-white rounded-xl shadow-sm border border-gray-100">
            <p class="text-4xl mb-3">💬</p>
            <p class="text-gray-500 text-base">上のボタンをタップして</p>
            <p class="text-gray-500 text-base">対話を始めましょう</p>
        </div>

        <!-- エラーメッセージ -->
        <div x-show="error" class="mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>
    </div>

    <!-- 保存ボタン（右下固定） -->
    <button
        type="button"
        @click="manualSave()"
        :disabled="submitting || floatingSaving || !isFormValid()"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center hover:from-purple-600 hover:to-indigo-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed z-30"
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
    let nextId = 1;

    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        entries: [],
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

                if (item && item.content) {
                    this.parseContent(item.content);
                }
            } catch (error) {
                this.error = 'データの読み込みに失敗しました。';
            } finally {
                this.loading = false;
            }
        },

        parseContent(raw) {
            try {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed)) {
                    this.entries = parsed.map(e => ({
                        id: nextId++,
                        type: e.type || 'healthy',
                        text: e.text || ''
                    }));
                    return;
                }
            } catch (e) {}

            if (raw.trim()) {
                this.entries = [{ id: nextId++, type: 'healthy', text: raw }];
            }
        },

        serializeContent() {
            return JSON.stringify(this.entries.map(e => ({
                type: e.type,
                text: e.text
            })));
        },

        addEntry(type) {
            this.entries.push({ id: nextId++, type: type, text: '' });
            this.$nextTick(() => {
                const container = this.$refs.entriesContainer;
                if (container) {
                    const cards = container.querySelectorAll('textarea');
                    const lastTextarea = cards[cards.length - 1];
                    if (lastTextarea) {
                        lastTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        lastTextarea.focus();
                    }
                }
            });
        },

        removeEntry(index) {
            this.entries.splice(index, 1);
        },

        onEntryInput(event, index) {
            const textarea = event.target;
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        },

        isFormValid() {
            return this.entries.length > 0 && this.entries.some(e => e.text.trim().length > 0);
        },

        getSnapshotString() {
            return JSON.stringify(this.entries.map(e => ({ type: e.type, text: e.text })));
        },

        takeSnapshot() {
            this.autoSaveSnapshots.push(this.getSnapshotString());

            if (this.autoSaveSnapshots.length > 2) {
                this.autoSaveSnapshots.shift();
            }
        },

        hasChangedFromPreviousSnapshot() {
            const current = this.getSnapshotString();
            if (this.autoSaveSnapshots.length < 2) {
                if (this.autoSaveSnapshots.length === 1) {
                    return current !== this.autoSaveSnapshots[0];
                }
                return false;
            }
            return current !== this.autoSaveSnapshots[0];
        },

        async checkAndAutoSave() {
            if (
                this.isFormValid() &&
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
                    body: JSON.stringify({ content: this.serializeContent() })
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

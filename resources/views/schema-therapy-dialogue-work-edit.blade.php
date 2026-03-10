@extends('layouts.app')

@section('title', 'ヘルシーサイドとスキーマサイドの対話のワーク - ココロの避難所')
@section('page-title', '対話のワーク')

@section('body-class', 'bg-gradient-to-b from-sky-100 to-sky-50')

@section('content')
<div x-data="dialogueWorkEditApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak class="pb-28">
    <!-- ヘッダー -->
    <div class="flex justify-between items-center mb-3">
        <a href="/schema-therapy/dialogue-work" class="text-gray-500 hover:text-gray-700 flex items-center gap-1 text-sm">
            ← 一覧に戻る
        </a>
        <button
            x-show="isEditMode"
            @click="confirmDelete()"
            class="text-red-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50/80 flex items-center gap-1 text-sm"
        >
            🗑️ <span class="hidden sm:inline">削除</span>
        </button>
    </div>

    <!-- ローディング -->
    <div x-show="loading && isEditMode" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 text-sky-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-500 mt-2 text-sm">読み込み中...</p>
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
        <!-- チャット風対話エリア -->
        <div class="space-y-4" x-ref="entriesContainer">
            <template x-for="(entry, index) in entries" :key="entry.id">
                <div
                    class="flex"
                    :class="entry.type === 'healthy' ? 'justify-start' : 'justify-end'"
                >
                    <div
                        class="max-w-[85%] sm:max-w-[75%]"
                        :class="entry.type === 'healthy' ? 'pr-4' : 'pl-4'"
                    >
                        <!-- 名前ラベル -->
                        <div
                            class="flex items-center gap-1.5 mb-1"
                            :class="entry.type === 'healthy' ? '' : 'justify-end'"
                        >
                            <!-- アイコン -->
                            <div
                                class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs flex-shrink-0"
                                :class="entry.type === 'healthy' ? 'bg-blue-500' : 'bg-rose-500'"
                                :style="entry.type === 'healthy' ? 'order: 0' : 'order: 1'"
                            >
                                <span x-text="entry.type === 'healthy' ? 'H' : 'S'"></span>
                            </div>
                            <span
                                class="text-xs font-bold"
                                :class="entry.type === 'healthy' ? 'text-blue-600' : 'text-rose-600'"
                                :style="entry.type === 'healthy' ? 'order: 1' : 'order: 0'"
                                x-text="entry.type === 'healthy' ? 'ヘルシーサイド' : 'スキーマサイド'"
                            ></span>
                        </div>

                        <!-- 吹き出し -->
                        <div class="relative">
                            <div
                                class="rounded-2xl shadow-sm p-0.5"
                                :class="entry.type === 'healthy'
                                    ? 'bg-white rounded-tl-sm'
                                    : 'bg-emerald-400 rounded-tr-sm'"
                            >
                                <textarea
                                    x-model="entry.text"
                                    rows="3"
                                    class="w-full rounded-2xl px-3.5 py-2.5 text-base leading-relaxed resize-none focus:outline-none"
                                    :class="entry.type === 'healthy'
                                        ? 'bg-white text-gray-800 placeholder-gray-400 rounded-tl-sm'
                                        : 'bg-emerald-400 text-white placeholder-emerald-100 rounded-tr-sm'"
                                    :placeholder="entry.type === 'healthy' ? 'ヘルシーサイドの言葉...' : 'スキーマサイドの言葉...'"
                                    @input="autoResize($event)"
                                    @focus="autoResize($event)"
                                ></textarea>
                            </div>
                            <!-- 削除ボタン -->
                            <button
                                type="button"
                                @click="removeEntry(index)"
                                class="absolute -top-2 rounded-full w-5 h-5 bg-gray-400 hover:bg-red-500 text-white flex items-center justify-center transition-colors shadow-sm"
                                :class="entry.type === 'healthy' ? '-right-2' : '-left-2'"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- 空の状態 -->
        <div x-show="entries.length === 0" class="text-center py-24">
            <p class="text-5xl mb-4">💬</p>
            <p class="text-gray-400 text-base mb-1">下のボタンをタップして</p>
            <p class="text-gray-400 text-base">対話を始めましょう</p>
        </div>

        <!-- エラーメッセージ -->
        <div x-show="error" class="mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>
    </div>

    <!-- 下部固定バー（話者ボタン＋保存） -->
    <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-sm border-t border-gray-200 px-4 py-3 z-30">
        <div class="max-w-4xl mx-auto flex items-center gap-2">
            <button
                type="button"
                @click="addEntry('healthy')"
                class="flex-1 py-2.5 bg-white border-2 border-blue-400 text-blue-600 rounded-full font-bold text-sm hover:bg-blue-50 transition-all"
            >
                + ヘルシーサイド
            </button>
            <button
                type="button"
                @click="addEntry('schema')"
                class="flex-1 py-2.5 bg-white border-2 border-rose-400 text-rose-600 rounded-full font-bold text-sm hover:bg-rose-50 transition-all"
            >
                + スキーマサイド
            </button>
            <button
                type="button"
                @click="manualSave()"
                :disabled="submitting || floatingSaving || !isFormValid()"
                class="w-11 h-11 flex-shrink-0 bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-full shadow-md flex items-center justify-center hover:from-purple-600 hover:to-indigo-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                title="保存する"
            >
                <template x-if="!floatingSaving && !submitting">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zM16 20v-6H8v6M8 4v4h6"></path>
                    </svg>
                </template>
                <template x-if="floatingSaving || submitting">
                    <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
            </button>
        </div>
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

        autoResize(event) {
            const el = event.target;
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
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

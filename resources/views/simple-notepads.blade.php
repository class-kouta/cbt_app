@extends('layouts.app')

@section('title', 'メモ帳')
@section('page-title', 'メモ帳')

@section('content')
<div x-data="simpleNotepadApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <!-- 手動保存トースト -->
    <div
        x-show="showSaveToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-16 right-4 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        保存しました
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

    <!-- エラートースト -->
    <div
        x-show="showErrorToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-16 right-4 bg-red-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span x-text="errorMessage"></span>
    </div>

    <!-- ヘッダー -->
    <div class="flex items-center justify-between mb-4">
        <a href="/simple-notepads/list" class="inline-flex items-center gap-1 text-green-600 hover:text-green-700 font-medium transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            一覧に戻る
        </a>
        <div class="flex items-center gap-2">
            <!-- 削除ボタン（編集モード時のみ） -->
            <button
                x-show="isEditMode"
                type="button"
                @click="deleteItem()"
                class="text-xl hover:opacity-70 transition-opacity"
                title="削除"
            >
                <x-icon name="trash" class="w-5 h-5" />
            </button>
            <!-- 編集する / 保存して編集をやめるボタン（編集モードのみ） -->
            <template x-if="isEditMode">
                <button
                    type="button"
                    @click="isEditing ? saveAndStopEditing() : startEditing()"
                    :disabled="isEditing && saving"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="isEditing
                        ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                        : 'bg-green-600 text-white hover:bg-green-700'"
                >
                    <template x-if="!isEditing">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </template>
                    <template x-if="isEditing && !saving">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="isEditing && saving">
                        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="isEditing ? (saving ? '保存中...' : '保存して編集をやめる') : '編集する'"></span>
                </button>
            </template>
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

    <!-- フォーム -->
    <div x-show="!loading" class="space-y-4">
        <!-- タイトル -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">タイトル</label>
            <input
                type="text"
                x-model="formData.title"
                :disabled="isEditMode && !isEditing"
                class="w-full border rounded-lg px-4 py-2 transition-all"
                :class="(isEditMode && !isEditing)
                    ? 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'
                    : 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white'"
                placeholder="タイトル（任意）"
                maxlength="255"
            >
        </div>

        <!-- メモ内容 -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">内容</label>
            <textarea
                x-model="formData.content"
                rows="18"
                :disabled="isEditMode && !isEditing"
                class="w-full border rounded-lg px-4 py-3 transition-all resize-y"
                :class="(isEditMode && !isEditing)
                    ? 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'
                    : 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white'"
                placeholder="なんでも自由に書いてください..."
                maxlength="10000"
            ></textarea>
            <div class="text-xs text-gray-400 text-right" x-text="(formData.content || '').length + '/10000'"></div>
        </div>

        <!-- エラーメッセージ -->
        <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

        <!-- 新規作成時のみ送信ボタン表示 -->
        <template x-if="!isEditMode">
            <div>
                <button
                    type="button"
                    @click="createSimpleNotepad()"
                    class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50"
                    :disabled="saving || !formData.content.trim()"
                >
                    <span x-show="!saving" class="inline-flex items-center justify-center gap-2"><x-icon name="arrow-down-tray" class="w-5 h-5" /> メモを保存</span>
                    <span x-show="saving" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        保存中...
                    </span>
                </button>
            </div>
        </template>
    </div>

    <!-- フローティング保存ボタン（編集モード時のみ） -->
    <button
        x-show="isEditMode && isEditing"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        type="button"
        @click="save()"
        :disabled="saving"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center hover:from-emerald-600 hover:to-teal-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed z-30"
        title="保存する"
    >
        <template x-if="!saving">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zM16 20v-6H8v6M8 4v4h6"></path>
            </svg>
        </template>
        <template x-if="saving">
            <svg class="animate-spin w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </template>
    </button>
</div>

<script>
function simpleNotepadApp(itemId) {
    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        isEditing: false,
        formData: {
            title: '',
            content: ''
        },
        loading: false,
        saving: false,
        error: '',
        showSaveToast: false,
        showAutoSaveToast: false,
        showErrorToast: false,
        errorMessage: '',

        autoSaveInterval: null,
        lastSavedState: null,

        async init() {
            if (this.isEditMode) {
                await this.loadItem();
            }
        },

        async loadItem() {
            this.loading = true;
            try {
                const res = await apiFetch('/api/simple-notepads');
                const items = await res.json();
                const item = items.find(i => i.id === this.itemId);
                if (item) {
                    this.formData.title = item.title || '';
                    this.formData.content = item.content || '';
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        startEditing() {
            this.isEditing = true;
            this.lastSavedState = {
                title: this.formData.title,
                content: this.formData.content
            };
            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        async saveAndStopEditing() {
            await this.performSave(true);
            this.stopEditing();
        },

        stopEditing() {
            this.isEditing = false;
            if (this.autoSaveInterval) {
                clearInterval(this.autoSaveInterval);
                this.autoSaveInterval = null;
            }
            this.lastSavedState = null;
        },

        async checkAndAutoSave() {
            if (!this.isEditing || this.saving) return;

            const currentState = {
                title: this.formData.title,
                content: this.formData.content
            };

            if (JSON.stringify(currentState) !== JSON.stringify(this.lastSavedState)) {
                const success = await this.performSave(false);
                if (success) {
                    this.lastSavedState = currentState;
                }
            }
        },

        async performSave(isManual) {
            if (this.saving) return false;

            if (!this.formData.content.trim()) {
                this.error = '内容を入力してください';
                return false;
            }

            this.saving = true;
            this.error = '';
            try {
                const res = await apiFetch(`/api/simple-notepads/${this.itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: this.formData.title,
                        content: this.formData.content
                    })
                });

                if (res.ok) {
                    if (isManual) {
                        this.showSaveToast = true;
                        setTimeout(() => { this.showSaveToast = false; }, 2000);
                    } else {
                        this.showAutoSaveToast = true;
                        setTimeout(() => { this.showAutoSaveToast = false; }, 2000);
                    }
                    return true;
                } else {
                    const errorData = await res.json();
                    this.errorMessage = errorData.message || '保存に失敗しました';
                    this.showErrorToast = true;
                    setTimeout(() => { this.showErrorToast = false; }, 3000);
                    return false;
                }
            } catch (error) {
                console.error('保存に失敗しました:', error);
                this.errorMessage = '保存に失敗しました';
                this.showErrorToast = true;
                setTimeout(() => { this.showErrorToast = false; }, 3000);
                return false;
            } finally {
                this.saving = false;
            }
        },

        async save() {
            const success = await this.performSave(true);
            if (success) {
                this.lastSavedState = {
                    title: this.formData.title,
                    content: this.formData.content
                };
            }
        },

        async createSimpleNotepad() {
            this.error = '';

            if (!this.formData.content.trim()) {
                this.error = '内容を入力してください';
                return;
            }

            this.saving = true;
            try {
                const res = await apiFetch('/api/simple-notepads', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: this.formData.title,
                        content: this.formData.content
                    })
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                window.location.href = '/simple-notepads/list';
            } catch (e) {
                this.error = e.message;
                this.saving = false;
            }
        },

        async deleteItem() {
            if (!confirm('このメモを削除しますか？')) return;

            try {
                await apiFetch(`/api/simple-notepads/${this.itemId}`, {
                    method: 'DELETE',
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

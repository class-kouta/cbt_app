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

    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <div x-show="!loading">
    <!-- ヘッダー -->
    <div class="flex items-center justify-between mb-4">
        <a href="/simple-notepads/list" class="inline-flex items-center gap-1 text-green-600 hover:text-green-700 font-medium transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            一覧に戻る
        </a>
        <div class="flex items-center gap-2">
            <!-- 削除ボタン（既存メモ編集時のみ） -->
            <button
                x-show="isEditMode"
                type="button"
                @click="deleteItem()"
                class="text-xl hover:opacity-70 transition-opacity"
                title="削除"
            >
                <x-icon name="trash" class="w-5 h-5" />
            </button>
        </div>
    </div>

    <!-- フォーム -->
    <div class="space-y-4">
        <!-- メモ内容 -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">内容</label>
            <textarea
                x-model="formData.content"
                rows="18"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base transition-all resize-y focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white"
                placeholder="なんでも自由に書いてください..."
                maxlength="10000"
            ></textarea>
            <div class="text-xs text-gray-400 text-right" x-text="(formData.content || '').length + '/10000'"></div>
        </div>

        <!-- タグセクション -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h3 class="text-base font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <x-icon name="tag" class="w-4 h-4" /> タグ
                <span class="text-gray-400 font-normal text-sm">（任意・複数選択可）</span>
            </h3>
            <p class="text-xs text-gray-500 mb-3">
                このメモに関連するタグを選択してください
            </p>

            <!-- タグ選択UI -->
            <div x-show="availableTags.length > 0" class="flex flex-wrap gap-2 mb-3">
                <template x-for="tag in availableTags" :key="tag.id">
                    <button
                        type="button"
                        @click="toggleTag(tag.id)"
                        class="px-3 py-1.5 text-sm rounded-full border transition-all"
                        :class="isTagSelected(tag.id)
                            ? 'bg-emerald-500 text-white border-emerald-500'
                            : 'bg-white text-gray-700 border-gray-300 hover:border-emerald-400 hover:bg-emerald-50'"
                        x-text="tag.name"
                    ></button>
                </template>
            </div>

            <!-- タグがない場合 -->
            <div x-show="availableTags.length === 0" class="text-sm text-gray-500 mb-3">
                タグがありません
            </div>

            <!-- 新規作成ページのみ：タグ追加フォーム -->
            <template x-if="!isEditMode">
                <div class="border-t border-gray-200 pt-3 mt-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-gray-500">新しいタグを追加</p>
                        <button
                            type="button"
                            @click="showNewTagForm = !showNewTagForm"
                            class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:border-emerald-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors"
                            :disabled="availableTags.length >= 10"
                            title="タグ追加フォームを表示"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    <div x-show="showNewTagForm" x-collapse>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                x-model="newTagName"
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-base focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="タグ名を入力..."
                                maxlength="10"
                                :disabled="availableTags.length >= 10 || addingTag"
                                @keydown.enter.prevent="addNewTag()"
                            >
                            <button
                                type="button"
                                @click="addNewTag()"
                                class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-base font-medium hover:bg-emerald-600 transition-colors disabled:opacity-50"
                                :disabled="!newTagName.trim() || availableTags.length >= 10 || addingTag"
                            >
                                <span x-show="!addingTag">追加</span>
                                <span x-show="addingTag">追加中...</span>
                            </button>
                        </div>
                        <div class="text-xs text-gray-400 text-right mt-1" x-text="newTagName.length + '/10'"></div>
                        <div x-show="tagError" class="text-red-500 text-xs mt-2" x-text="tagError"></div>
                    </div>
                    <div x-show="availableTags.length >= 10" class="text-amber-600 text-xs mt-2">
                        タグは10個までしか作成できません。新しく作成する場合は<a href="/simple-notepad-tags" class="underline text-emerald-600 hover:text-emerald-700">タグ管理</a>から既存のタグを削除するかタグ名を変更してください。
                    </div>
                </div>
            </template>
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
    </div>

    <!-- フローティング保存ボタン（既存メモ編集時のみ） -->
    <button
        x-show="isEditMode"
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
        formData: {
            content: '',
            tag_ids: []
        },
        availableTags: [],
        newTagName: '',
        showNewTagForm: false,
        addingTag: false,
        tagError: '',
        loading: true,
        saving: false,
        error: '',
        showSaveToast: false,
        showErrorToast: false,
        errorMessage: '',

        async init() {
            this.loading = true;
            try {
                await this.loadTags();

                if (this.isEditMode) {
                    await this.loadItem();
                }
            } finally {
                this.loading = false;
            }
        },

        async loadTags() {
            try {
                const res = await apiFetch('/api/simple-notepad-tags');
                if (res.ok) {
                    this.availableTags = await res.json();
                }
            } catch (error) {
                console.error('タグの取得に失敗しました:', error);
            }
        },

        toggleTag(tagId) {
            const index = this.formData.tag_ids.indexOf(tagId);
            if (index > -1) {
                this.formData.tag_ids.splice(index, 1);
            } else {
                if (this.formData.tag_ids.length >= 10) {
                    alert('タグは10個までしか選択できません');
                    return;
                }
                this.formData.tag_ids.push(tagId);
            }
        },

        isTagSelected(tagId) {
            return this.formData.tag_ids.includes(tagId);
        },

        async addNewTag() {
            this.tagError = '';

            if (!this.newTagName.trim()) {
                this.tagError = 'タグ名を入力してください';
                return;
            }

            if (this.availableTags.length >= 10) {
                this.tagError = 'タグは10個までしか作成できません。新しく作成する場合はタグ管理から既存のタグを削除するかタグ名を変更してください。';
                return;
            }

            this.addingTag = true;
            try {
                const res = await apiFetch('/api/simple-notepad-tags', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name: this.newTagName.trim() })
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || (data.errors?.name?.[0]) || 'エラーが発生しました');
                }

                const newTag = await res.json();
                this.newTagName = '';
                this.showNewTagForm = false;
                await this.loadTags();
                if (!this.formData.tag_ids.includes(newTag.id)) {
                    if (this.formData.tag_ids.length >= 10) {
                        this.tagError = 'タグは10個までしか選択できません';
                    } else {
                        this.formData.tag_ids.push(newTag.id);
                    }
                }
            } catch (e) {
                this.tagError = e.message;
            } finally {
                this.addingTag = false;
            }
        },

        async loadItem() {
            try {
                const res = await apiFetch(`/api/simple-notepads/${this.itemId}`);
                const item = await res.json();
                this.formData.content = item.content || '';
                this.formData.tag_ids = item.tag_ids || [];
            } catch (error) {
                console.error(error);
            }
        },

        async performSave() {
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
                        content: this.formData.content,
                        tag_ids: this.formData.tag_ids
                    })
                });

                if (res.ok) {
                    this.showSaveToast = true;
                    setTimeout(() => { this.showSaveToast = false; }, 2000);
                    return true;
                } else {
                    this.errorMessage = '保存に失敗しました';
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
            await this.performSave();
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
                        content: this.formData.content,
                        tag_ids: this.formData.tag_ids
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

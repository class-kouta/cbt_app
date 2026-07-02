@extends('layouts.app')

@section('title', 'タグ管理')
@section('page-title', 'タグ管理')

@section('content')
<div x-data="simpleNotepadTagApp()" x-init="init()" x-cloak>
    <!-- ローディング -->
    <div x-show="pageLoading" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <div x-show="!pageLoading">
        <!-- 新規タグ作成フォーム -->
        <div class="mb-6">
            <form @submit.prevent="createTag()">
                <div class="space-y-4">
                    <div>
                        <input
                            type="text"
                            x-model="newTag.name"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-2 focus:ring-emerald-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
                            placeholder="タグ名を入力..."
                            maxlength="10"
                            :disabled="loading || tags.length >= 10"
                            required
                        >
                        <div class="text-xs text-gray-400 text-right" x-text="newTag.name.length + '/10'"></div>
                    </div>

                    <div>
                        <div class="grid grid-cols-10 w-full">
                            <template x-for="colorKey in colorKeys" :key="'new-' + colorKey">
                                <button
                                    type="button"
                                    @click="newTag.color = colorKey"
                                    class="w-5 h-5 mx-auto rounded-full border transition-all"
                                    :class="[
                                        getSimpleNotepadTagColor(colorKey).selectedBg,
                                        newTag.color === colorKey
                                            ? 'border-gray-700 ring-1 ring-offset-1 ring-gray-400'
                                            : 'border-transparent opacity-75 hover:opacity-100'
                                    ]"
                                    :title="colorKey"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <div x-show="tags.length >= 10" class="text-amber-600 text-sm">
                        タグの作成は10個までです。新しく作成したい場合は既存のタグを削除するか、タグ名を変更してください。
                    </div>

                    <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

                    <div>
                        <button
                            type="submit"
                            class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50"
                            :disabled="loading || !newTag.name.trim() || tags.length >= 10"
                        >
                            <span x-show="!loading">追加</span>
                            <span x-show="loading">追加中...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- タグ一覧 -->
        <div class="space-y-3">
            <div class="text-sm text-gray-600 mb-2">
                合計: <span x-text="tags.length" class="font-bold"></span> / 10 個
            </div>

            <template x-for="tag in tags" :key="tag.id">
                <div class="bg-white rounded-lg shadow-md p-4 transition-all hover:shadow-lg">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 min-w-0">
                            <!-- 編集モード -->
                            <div x-show="editingId === tag.id">
                                <input
                                    type="text"
                                    x-model="editName"
                                    x-effect="if (editingId === tag.id) { $nextTick(() => { $el.focus(); $el.select(); }) }"
                                    @keydown.escape="cancelEdit()"
                                    @keydown.enter="saveEdit(tag)"
                                    class="w-full border border-emerald-400 rounded-lg px-3 py-2 text-base focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-emerald-50"
                                    maxlength="10"
                                >
                                <div class="text-xs text-gray-400 text-right mt-1" x-text="editName.length + '/10'"></div>
                                <div class="mt-3">
                                    <div class="grid grid-cols-10 w-full">
                                        <template x-for="colorKey in colorKeys" :key="'edit-' + tag.id + '-' + colorKey">
                                            <button
                                                type="button"
                                                @click="editColor = colorKey"
                                                class="w-5 h-5 mx-auto rounded-full border transition-all"
                                                :class="[
                                                    getSimpleNotepadTagColor(colorKey).selectedBg,
                                                    editColor === colorKey
                                                        ? 'border-gray-700 ring-1 ring-offset-1 ring-gray-400'
                                                        : 'border-transparent opacity-75 hover:opacity-100'
                                                ]"
                                                :title="colorKey"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <button
                                        type="button"
                                        @click="saveEdit(tag)"
                                        class="text-emerald-600 hover:text-emerald-700 transition-colors p-1.5 rounded hover:bg-emerald-50"
                                        title="保存"
                                    >
                                        <x-icon name="floppy-disk" class="w-5 h-5" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteTag(tag)"
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

                            <!-- 表示モード -->
                            <div
                                x-show="editingId !== tag.id"
                                @click="startEdit(tag)"
                                class="cursor-pointer hover:bg-gray-50 rounded-lg p-1 -m-1 transition-colors"
                                title="クリックして編集"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                                        :class="getSimpleNotepadTagColor(tag).iconBg + ' ' + getSimpleNotepadTagColor(tag).iconText"
                                    >
                                        <x-icon name="tag" class="w-5 h-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-gray-800 break-words overflow-wrap-anywhere font-medium" x-text="tag.name"></p>
                                        <p x-show="tag.usage_count > 0" class="text-xs text-gray-500 mt-0.5" x-text="tag.usage_count + '件のメモで使用中'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- 空の状態 -->
            <div x-show="tags.length === 0" class="text-center py-12 text-gray-500">
                <div class="mb-4 flex justify-center text-gray-300"><x-icon name="tag" class="w-12 h-12" /></div>
                <p>タグがありません</p>
                <p class="text-sm mt-2">メモを整理するためのタグを作成しましょう</p>
            </div>
        </div>
    </div>
</div>

<script>
function simpleNotepadTagApp() {
    return {
        tags: [],
        colorKeys: SIMPLE_NOTEPAD_TAG_COLOR_KEYS,
        newTag: {
            name: '',
        },
        editingId: null,
        editName: '',
        editColor: 'emerald',
        loading: false,
        pageLoading: true,
        error: '',

        async init() {
            this.pageLoading = true;
            try {
                await this.loadTags();
                this.resetNewTagColor();
            } finally {
                this.pageLoading = false;
            }
        },

        resetNewTagColor() {
            this.newTag.color = defaultSimpleNotepadTagColor(this.tags.length);
        },

        async loadTags() {
            const res = await apiFetch('/api/simple-notepad-tags');
            this.tags = await res.json();
        },

        getSimpleNotepadTagColor(tag) {
            return getSimpleNotepadTagColor(tag);
        },

        async createTag() {
            this.error = '';

            if (!this.newTag.name.trim()) {
                this.error = 'タグ名を入力してください';
                return;
            }

            if (this.tags.length >= 10) {
                this.error = 'タグの作成は10個までです。新しく作成したい場合は既存のタグを削除するか、タグ名を変更してください。';
                return;
            }

            this.loading = true;
            try {
                const res = await apiFetch('/api/simple-notepad-tags', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.newTag.name.trim(),
                        color: this.newTag.color,
                    })
                });

                if (!res.ok) {
                    const data = await res.json();
                    const message = data.message || (data.errors?.name?.[0]) || (data.errors?.color?.[0]) || 'エラーが発生しました';
                    throw new Error(message);
                }

                this.newTag.name = '';
                await this.loadTags();
                this.resetNewTagColor();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        startEdit(tag) {
            this.editingId = tag.id;
            this.editName = tag.name;
            this.editColor = tag.color || defaultSimpleNotepadTagColor(tag.id);
        },

        cancelEdit() {
            this.editingId = null;
            this.editName = '';
            this.editColor = 'emerald';
        },

        confirmInUseAction(tag, actionLabel) {
            if (tag.usage_count > 0) {
                return confirm(`このタグは${tag.usage_count}件のメモで使用されています。${actionLabel}してもよろしいですか？`);
            }

            if (actionLabel === '削除') {
                return confirm('このタグを削除しますか？');
            }

            return true;
        },

        async saveEdit(tag) {
            const trimmedName = this.editName.trim();
            if (!trimmedName) {
                alert('タグ名を入力してください');
                return;
            }

            if (trimmedName === tag.name && this.editColor === (tag.color || defaultSimpleNotepadTagColor(tag.id))) {
                this.cancelEdit();
                return;
            }

            if (!this.confirmInUseAction(tag, '更新')) {
                return;
            }

            try {
                const res = await apiFetch(`/api/simple-notepad-tags/${tag.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: trimmedName,
                        color: this.editColor,
                    })
                });

                if (!res.ok) {
                    const data = await res.json();
                    const message = data.message || (data.errors?.name?.[0]) || (data.errors?.color?.[0]) || 'エラーが発生しました';
                    alert(message);
                    return;
                }

                this.cancelEdit();
                await this.loadTags();
            } catch (e) {
                console.error(e);
            }
        },

        async deleteTag(tag) {
            if (!this.confirmInUseAction(tag, '削除')) return;

            try {
                await apiFetch(`/api/simple-notepad-tags/${tag.id}`, {
                    method: 'DELETE',
                });
                await this.loadTags();
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection

@extends('layouts.app')

@section('title', 'TODOタグ管理')

@section('content')
<div x-data="tagManagement()" x-init="init()" x-cloak class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">🏷️ TODOタグ管理</h1>

        <!-- 新規タグ追加フォーム -->
        <div class="bg-gray-50 rounded-xl p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">➕ 新しいタグを追加</h2>
            <form @submit.prevent="createTag()" class="flex gap-4">
                <div class="flex-1">
                    <input
                        type="text"
                        x-model="newTagName"
                        placeholder="タグ名を入力（例：個人開発、家事、育児）"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        :disabled="creating"
                        maxlength="50"
                    >
                </div>
                <button
                    type="submit"
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-colors disabled:opacity-50"
                    :disabled="creating || !newTagName.trim()"
                >
                    <span x-show="!creating">追加</span>
                    <span x-show="creating">追加中...</span>
                </button>
            </form>
            <div x-show="createError" class="text-red-500 text-sm mt-2" x-text="createError"></div>
        </div>

        <!-- 成功メッセージ -->
        <div x-show="successMessage" x-transition class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <span x-text="successMessage"></span>
        </div>

        <!-- タグ一覧 -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">📋 タグ一覧</h2>
            
            <!-- ローディング -->
            <div x-show="loading" class="text-center py-8">
                <p class="text-gray-500">読み込み中...</p>
            </div>

            <!-- タグがない場合 -->
            <div x-show="!loading && tags.length === 0" class="text-center py-8 text-gray-500">
                <p class="text-4xl mb-4">📭</p>
                <p>タグがありません</p>
            </div>

            <!-- タグ一覧テーブル -->
            <div x-show="!loading && tags.length > 0" class="space-y-3">
                <template x-for="tag in tags" :key="tag.id">
                    <div
                        class="border rounded-lg p-4 flex items-center gap-4"
                        :class="tag.deleted_at ? 'bg-gray-100 border-gray-300' : 'bg-white border-gray-200'"
                    >
                        <!-- 編集モード -->
                        <template x-if="editingTagId === tag.id">
                            <div class="flex-1 flex items-center gap-4">
                                <input
                                    type="text"
                                    x-model="editingTagName"
                                    class="flex-1 border border-indigo-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    maxlength="50"
                                    @keyup.enter="updateTag(tag)"
                                    @keyup.escape="cancelEdit()"
                                >
                                <button
                                    @click="updateTag(tag)"
                                    class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600 transition-colors"
                                    :disabled="updating"
                                >
                                    保存
                                </button>
                                <button
                                    @click="cancelEdit()"
                                    class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-500 transition-colors"
                                >
                                    キャンセル
                                </button>
                            </div>
                        </template>

                        <!-- 通常表示 -->
                        <template x-if="editingTagId !== tag.id">
                            <div class="flex-1 flex items-center gap-4">
                                <span
                                    class="text-lg"
                                    :class="tag.deleted_at ? 'text-gray-400 line-through' : 'text-gray-800'"
                                    x-text="tag.name"
                                ></span>
                                <span
                                    x-show="tag.deleted_at"
                                    class="inline-block px-2 py-0.5 rounded text-xs bg-red-100 text-red-600"
                                >
                                    削除済み
                                </span>
                            </div>
                        </template>

                        <!-- アクションボタン -->
                        <div class="flex gap-2" x-show="editingTagId !== tag.id">
                            <!-- 削除されていない場合 -->
                            <template x-if="!tag.deleted_at">
                                <div class="flex gap-2">
                                    <button
                                        @click="startEdit(tag)"
                                        class="text-indigo-500 hover:text-indigo-700 transition-colors px-3 py-1 rounded border border-indigo-200 hover:bg-indigo-50"
                                        title="編集"
                                    >
                                        ✏️ 編集
                                    </button>
                                    <button
                                        @click="deleteTag(tag)"
                                        class="text-red-500 hover:text-red-700 transition-colors px-3 py-1 rounded border border-red-200 hover:bg-red-50"
                                        title="削除"
                                    >
                                        🗑️ 削除
                                    </button>
                                </div>
                            </template>

                            <!-- 削除されている場合 -->
                            <template x-if="tag.deleted_at">
                                <button
                                    @click="restoreTag(tag)"
                                    class="text-green-500 hover:text-green-700 transition-colors px-3 py-1 rounded border border-green-200 hover:bg-green-50"
                                    title="復元"
                                >
                                    ♻️ 復元
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 説明 -->
        <div class="bg-blue-50 rounded-xl p-4 text-sm text-blue-700">
            <p class="font-semibold mb-2">💡 ヒント</p>
            <ul class="list-disc list-inside space-y-1">
                <li>削除されたタグはTODO作成画面で選択できなくなります</li>
                <li>削除されたタグは完了TODO一覧でも表示されなくなります</li>
                <li>削除されたタグは「復元」ボタンで元に戻せます</li>
            </ul>
        </div>

        <div class="mt-8 text-center">
            <a href="/siteAdmPanel63/todo/menu" class="text-indigo-600 hover:text-indigo-800 text-sm">← TODO管理メニューに戻る</a>
        </div>
    </div>
</div>

<script>
function tagManagement() {
    return {
        tags: [],
        loading: false,
        creating: false,
        updating: false,
        newTagName: '',
        createError: '',
        successMessage: '',
        editingTagId: null,
        editingTagName: '',

        async init() {
            await this.loadTags();
        },

        async loadTags() {
            this.loading = true;
            try {
                const res = await fetch('/api/tags/with-trashed');
                this.tags = await res.json();
            } catch (e) {
                console.error('タグの読み込みに失敗しました:', e);
            } finally {
                this.loading = false;
            }
        },

        async createTag() {
            if (!this.newTagName.trim()) return;

            this.creating = true;
            this.createError = '';

            try {
                const res = await fetch('/api/tags', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.newTagName.trim() })
                });

                if (!res.ok) {
                    const data = await res.json();
                    if (data.errors && data.errors.name) {
                        throw new Error(data.errors.name[0]);
                    }
                    throw new Error(data.message || 'エラーが発生しました');
                }

                this.newTagName = '';
                this.showSuccess('タグを追加しました！');
                await this.loadTags();
            } catch (e) {
                this.createError = e.message;
            } finally {
                this.creating = false;
            }
        },

        startEdit(tag) {
            this.editingTagId = tag.id;
            this.editingTagName = tag.name;
        },

        cancelEdit() {
            this.editingTagId = null;
            this.editingTagName = '';
        },

        async updateTag(tag) {
            if (!this.editingTagName.trim()) return;

            this.updating = true;

            try {
                const res = await fetch(`/api/tags/${tag.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.editingTagName.trim() })
                });

                if (!res.ok) {
                    const data = await res.json();
                    if (data.errors && data.errors.name) {
                        throw new Error(data.errors.name[0]);
                    }
                    throw new Error(data.message || 'エラーが発生しました');
                }

                this.cancelEdit();
                this.showSuccess('タグを更新しました！');
                await this.loadTags();
            } catch (e) {
                alert(e.message);
            } finally {
                this.updating = false;
            }
        },

        async deleteTag(tag) {
            if (!confirm(`タグ「${tag.name}」を削除しますか？\n削除されたタグはTODO作成画面で選択できなくなります。`)) {
                return;
            }

            try {
                const res = await fetch(`/api/tags/${tag.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    this.showSuccess('タグを削除しました');
                    await this.loadTags();
                }
            } catch (e) {
                console.error('削除に失敗しました:', e);
            }
        },

        async restoreTag(tag) {
            if (!confirm(`タグ「${tag.name}」を復元しますか？`)) {
                return;
            }

            try {
                const res = await fetch(`/api/tags/${tag.id}/restore`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    this.showSuccess('タグを復元しました！');
                    await this.loadTags();
                }
            } catch (e) {
                console.error('復元に失敗しました:', e);
            }
        },

        showSuccess(message) {
            this.successMessage = message;
            setTimeout(() => {
                this.successMessage = '';
            }, 3000);
        }
    };
}
</script>
@endsection

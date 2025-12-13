@extends('layouts.app')

@section('title', 'クイックタスク管理')

@section('content')
<div x-data="quickTaskApp()" x-init="init()" x-cloak>
    <!-- ページタイトルと説明 -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">⚡ クイックタスク</h1>
        <p class="text-gray-600 text-sm">よく使うタスクを登録しておくと便利です！</p>
    </div>

    <!-- 新規クイックタスク作成フォーム -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form @submit.prevent="createQuickTask()">
            <div class="space-y-4">
                <!-- 内容 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">タスク内容 <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        x-model="newQuickTask.content"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="例: お風呂掃除、洗濯物たたみ..."
                        maxlength="200"
                        required
                    >
                    <div class="text-xs text-gray-400 text-right" x-text="newQuickTask.content.length + '/200'"></div>
                </div>

                <!-- 難易度選択 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">難易度（任意）</label>
                    <div class="flex gap-3">
                        <template x-for="difficulty in difficulties" :key="difficulty.id">
                            <label
                                class="flex-1 cursor-pointer"
                                :class="{'ring-2 ring-offset-2': newQuickTask.difficulty_id === difficulty.id}"
                                :style="{ 'ring-color': difficulty.color }"
                            >
                                <input
                                    type="radio"
                                    :value="difficulty.id"
                                    x-model.number="newQuickTask.difficulty_id"
                                    class="sr-only"
                                >
                                <div
                                    class="text-center py-2 px-3 rounded-lg border-2 transition-all hover:shadow-md"
                                    :style="{ 'border-color': difficulty.color, 'background-color': newQuickTask.difficulty_id === difficulty.id ? difficulty.color + '20' : 'white' }"
                                >
                                    <span class="font-semibold text-sm" :style="{ color: difficulty.color }" x-text="difficulty.name"></span>
                                </div>
                            </label>
                        </template>
                        <button
                            type="button"
                            @click="newQuickTask.difficulty_id = null"
                            class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
                            x-show="newQuickTask.difficulty_id !== null"
                        >
                            クリア
                        </button>
                    </div>
                </div>

                <!-- タグ選択 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">タグ（任意）</label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in tags" :key="tag.id">
                            <label class="cursor-pointer">
                                <input
                                    type="checkbox"
                                    :value="tag.id"
                                    x-model.number="newQuickTask.tag_ids"
                                    class="sr-only"
                                >
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-sm border-2 transition-all"
                                    :class="newQuickTask.tag_ids.includes(tag.id) ? 'bg-indigo-500 text-white border-indigo-500' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-300'"
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
                        :disabled="loading || !newQuickTask.content.trim()"
                    >
                        <span x-show="!loading">登録する</span>
                        <span x-show="loading">登録中...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- クイックタスク一覧 -->
    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2">
            登録済み: <span x-text="quickTasks.length" class="font-bold"></span> 件
        </div>

        <template x-for="task in quickTasks" :key="task.id">
            <div class="bg-white rounded-lg shadow-md p-4 transition-all hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <!-- アイコン -->
                    <div class="flex-shrink-0 text-2xl">⚡</div>

                    <!-- 内容 -->
                    <div class="flex-1 min-w-0">
                        <!-- 編集モード -->
                        <div x-show="editingId === task.id">
                            <input
                                type="text"
                                x-model="editContent"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent mb-2"
                                maxlength="200"
                            >
                            <!-- 編集時の難易度選択 -->
                            <div class="flex flex-wrap gap-2 mb-2">
                                <template x-for="difficulty in difficulties" :key="difficulty.id">
                                    <label class="cursor-pointer">
                                        <input
                                            type="radio"
                                            :value="difficulty.id"
                                            x-model.number="editDifficultyId"
                                            class="sr-only"
                                        >
                                        <span
                                            class="inline-block px-2 py-0.5 rounded text-xs font-semibold transition-all"
                                            :style="{
                                                'background-color': editDifficultyId === difficulty.id ? difficulty.color : 'transparent',
                                                'color': editDifficultyId === difficulty.id ? 'white' : difficulty.color,
                                                'border': '1px solid ' + difficulty.color
                                            }"
                                            x-text="difficulty.name"
                                        ></span>
                                    </label>
                                </template>
                                <button
                                    type="button"
                                    @click="editDifficultyId = null"
                                    class="text-xs text-gray-500 hover:text-gray-700"
                                    x-show="editDifficultyId !== null"
                                >
                                    ✕
                                </button>
                            </div>
                            <!-- 編集時のタグ選択 -->
                            <div class="flex flex-wrap gap-2 mb-2">
                                <template x-for="tag in tags" :key="tag.id">
                                    <label class="cursor-pointer">
                                        <input
                                            type="checkbox"
                                            :value="tag.id"
                                            x-model.number="editTagIds"
                                            class="sr-only"
                                        >
                                        <span
                                            class="inline-block px-2 py-0.5 rounded-full text-xs border transition-all"
                                            :class="editTagIds.includes(tag.id) ? 'bg-indigo-500 text-white border-indigo-500' : 'bg-white text-gray-600 border-gray-300'"
                                            x-text="tag.name"
                                        ></span>
                                    </label>
                                </template>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    @click="saveEdit(task)"
                                    class="bg-emerald-500 text-white px-3 py-1 rounded text-sm hover:bg-emerald-600"
                                >
                                    保存
                                </button>
                                <button
                                    @click="cancelEdit()"
                                    class="bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-400"
                                >
                                    キャンセル
                                </button>
                            </div>
                        </div>

                        <!-- 表示モード -->
                        <div x-show="editingId !== task.id">
                            <p class="text-gray-800 break-words overflow-wrap-anywhere" x-text="task.content"></p>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <!-- 難易度バッジ -->
                                <span
                                    x-show="task.difficulty"
                                    class="inline-block px-2 py-0.5 rounded text-xs font-semibold text-white"
                                    :style="{ 'background-color': task.difficulty?.color || '#999' }"
                                    x-text="task.difficulty?.name"
                                ></span>
                                <!-- タグ -->
                                <template x-for="tag in task.tags" :key="tag.id">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs bg-gray-200 text-gray-600" x-text="tag.name"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- 操作ボタン -->
                    <div class="flex gap-2" x-show="editingId !== task.id">
                        <button
                            @click="startEdit(task)"
                            class="text-gray-400 hover:text-indigo-600 transition-colors"
                            title="編集"
                        >
                            ✏️
                        </button>
                        <button
                            @click="deleteQuickTask(task)"
                            class="text-gray-400 hover:text-red-600 transition-colors"
                            title="削除"
                        >
                            🗑️
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- 空の状態 -->
        <div x-show="quickTasks.length === 0" class="text-center py-12 text-gray-500">
            <p class="text-4xl mb-4">⚡</p>
            <p>クイックタスクがまだありません</p>
            <p class="text-sm mt-2">よく使うタスクを登録してみましょう！</p>
        </div>
    </div>
</div>

<script>
function quickTaskApp() {
    return {
        quickTasks: [],
        difficulties: [],
        tags: [],
        newQuickTask: {
            content: '',
            difficulty_id: null,
            tag_ids: []
        },
        editingId: null,
        editContent: '',
        editDifficultyId: null,
        editTagIds: [],
        loading: false,
        error: '',

        async init() {
            await Promise.all([
                this.loadQuickTasks(),
                this.loadDifficulties(),
                this.loadTags()
            ]);
        },

        async loadQuickTasks() {
            const res = await fetch('/api/quick-tasks');
            this.quickTasks = await res.json();
        },

        async loadDifficulties() {
            const res = await fetch('/api/difficulties');
            this.difficulties = await res.json();
        },

        async loadTags() {
            const res = await fetch('/api/tags');
            this.tags = await res.json();
        },

        async createQuickTask() {
            this.error = '';

            if (!this.newQuickTask.content.trim()) {
                this.error = 'タスク内容を入力してください';
                return;
            }

            this.loading = true;
            try {
                const res = await fetch('/api/quick-tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newQuickTask)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                this.newQuickTask = { content: '', difficulty_id: null, tag_ids: [] };
                await this.loadQuickTasks();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        startEdit(task) {
            this.editingId = task.id;
            this.editContent = task.content;
            this.editDifficultyId = task.difficulty_id;
            this.editTagIds = task.tags.map(t => t.id);
        },

        cancelEdit() {
            this.editingId = null;
            this.editContent = '';
            this.editDifficultyId = null;
            this.editTagIds = [];
        },

        async saveEdit(task) {
            if (!this.editContent.trim()) {
                alert('タスク内容を入力してください');
                return;
            }

            try {
                const res = await fetch(`/api/quick-tasks/${task.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.editContent,
                        difficulty_id: this.editDifficultyId,
                        tag_ids: this.editTagIds
                    })
                });

                if (res.ok) {
                    this.cancelEdit();
                    await this.loadQuickTasks();
                }
            } catch (e) {
                console.error(e);
            }
        },

        async deleteQuickTask(task) {
            if (!confirm('このクイックタスクを削除しますか？')) return;

            try {
                await fetch(`/api/quick-tasks/${task.id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                await this.loadQuickTasks();
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection

@extends('layouts.app')

@section('title', 'TODOリスト')

@section('content')
<div x-data="todoApp()" x-init="init()" x-cloak>
    <!-- リンク -->
    <div class="mb-4 flex justify-end">
        <a href="/todos/completed" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 transition-colors">
            完了済み →
        </a>
    </div>

    <!-- 新規TODO作成フォーム -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form @submit.prevent="createTodo()">
            <div class="space-y-4">
                <!-- 内容 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Todo</label>
                    <textarea
                        x-model="newTodo.content"
                        rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="やることを入力してください"
                        required
                    ></textarea>
                </div>

                <!-- 難易度選択 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">難易度 <span class="text-red-500">*</span></label>
                    <div class="flex gap-3">
                        <template x-for="difficulty in difficulties" :key="difficulty.id">
                            <label
                                class="flex-1 cursor-pointer"
                                :class="{'ring-2 ring-offset-2': newTodo.difficulty_id === difficulty.id}"
                                :style="{ 'ring-color': difficulty.color }"
                            >
                                <input
                                    type="radio"
                                    :value="difficulty.id"
                                    x-model.number="newTodo.difficulty_id"
                                    class="sr-only"
                                    required
                                >
                                <div
                                    class="text-center py-3 px-4 rounded-lg border-2 transition-all hover:shadow-md"
                                    :style="{ 'border-color': difficulty.color, 'background-color': newTodo.difficulty_id === difficulty.id ? difficulty.color + '20' : 'white' }"
                                >
                                    <span class="font-semibold" :style="{ color: difficulty.color }" x-text="difficulty.name"></span>
                                    <span class="text-xs text-gray-500 block" x-text="difficulty.points + 'pt'"></span>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- タグ選択 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">タグ</label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in tags" :key="tag.id">
                            <label class="cursor-pointer">
                                <input
                                    type="checkbox"
                                    :value="tag.id"
                                    x-model.number="newTodo.tag_ids"
                                    class="sr-only"
                                >
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-sm border-2 transition-all"
                                    :class="newTodo.tag_ids.includes(tag.id) ? 'bg-indigo-500 text-white border-indigo-500' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-300'"
                                    x-text="tag.name"
                                ></span>
                            </label>
                        </template>
                    </div>
                    <p x-show="tags.length === 0" class="text-xs text-gray-500 mt-1">タグがありません。<a href="/siteAdmPanel63/todo/tag" class="text-indigo-600 hover:text-indigo-800 underline">追加する</a></p>
                </div>

                <!-- クイックタスク -->
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <label class="block text-sm font-medium text-gray-700">⚡ クイックタスク</label>
                        <a href="/quick-tasks" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">✏️ 編集</a>
                    </div>
                    <div x-show="quickTasks.length > 0">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="quickTask in quickTasks" :key="quickTask.id">
                                <button
                                    type="button"
                                    @click="applyQuickTask(quickTask)"
                                    class="inline-block px-3 py-1 rounded-full text-sm border-2 transition-all bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100 hover:border-emerald-400"
                                    x-text="quickTask.content"
                                ></button>
                            </template>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">※ タップするとフォームに入力されます</p>
                    </div>
                    <p x-show="quickTasks.length === 0" class="text-xs text-gray-500">クイックタスクがありません。<a href="/quick-tasks" class="text-indigo-600 hover:text-indigo-800 underline">追加する</a></p>
                </div>

                <!-- エラーメッセージ -->
                <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

                <!-- 送信ボタン -->
                <div>
                    <button
                        type="submit"
                        class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition-colors disabled:opacity-50"
                        :disabled="loading"
                    >
                        <span x-show="!loading">追加</span>
                        <span x-show="loading">作成中...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- フィルター -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div>
                <label class="text-sm font-medium text-gray-700 mr-2">表示:</label>
                <select x-model="filter" class="border border-gray-300 rounded-lg px-3 py-1">
                    <option value="all">すべて</option>
                    <option value="active">未完了のみ</option>
                    <option value="completed">完了済みのみ</option>
                </select>
            </div>
            <div class="text-sm text-gray-600">
                合計: <span x-text="filteredTodos.length" class="font-bold"></span> 件
            </div>
        </div>
    </div>

    <!-- TODO一覧 -->
    <div class="space-y-4">
        <template x-for="todo in filteredTodos" :key="todo.id">
            <div
                class="bg-white rounded-lg shadow-md p-4 transition-all"
                :class="{ 'opacity-60': todo.completed_at }"
            >
                <div class="flex items-start gap-4">
                    <!-- 完了チェックボックス -->
                    <div class="pt-1 flex items-center">
                        <input
                            type="checkbox"
                            @change="completeTodo(todo)"
                            :checked="todo.completed_at"
                            :disabled="todo.completed_at"
                            class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer disabled:cursor-not-allowed"
                            title="完了にする"
                        >
                    </div>

                    <!-- 内容 -->
                    <div class="flex-1 min-w-0">
                        <p
                            class="text-gray-800 break-words overflow-wrap-anywhere"
                            :class="{ 'line-through': todo.completed_at }"
                            x-text="todo.content"
                        ></p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <!-- 難易度バッジ -->
                            <span
                                class="inline-block px-2 py-0.5 rounded text-xs font-semibold text-white"
                                :style="{ 'background-color': todo.difficulty?.color || '#999' }"
                                x-text="todo.difficulty?.name || '不明'"
                            ></span>
                            <!-- タグ -->
                            <template x-for="tag in todo.tags" :key="tag.id">
                                <span class="inline-block px-2 py-0.5 rounded text-xs bg-gray-200 text-gray-600" x-text="tag.name"></span>
                            </template>
                        </div>
                        <div class="text-xs text-gray-400 mt-2">
                            <span x-text="formatDate(todo.created_at)"></span>
                            <span x-show="todo.completed_at" class="ml-2 text-green-600">
                                ✅ <span x-text="formatDate(todo.completed_at)"></span> に完了
                            </span>
                        </div>
                    </div>

                    <!-- 削除ボタン -->
                    <button
                        @click="deleteTodo(todo)"
                        class="text-red-400 hover:text-red-600 transition-colors"
                        title="削除"
                    >
                        🗑️
                    </button>
                </div>
            </div>
        </template>

        <!-- 空の状態 -->
        <div x-show="filteredTodos.length === 0" class="text-center py-12 text-gray-500">
            <p class="text-4xl mb-4">📭</p>
            <p>TODOがありません</p>
        </div>
    </div>
</div>

<script>
function todoApp() {
    return {
        todos: [],
        difficulties: [],
        tags: [],
        quickTasks: [],
        newTodo: {
            content: '',
            difficulty_id: null,
            tag_ids: []
        },
        filter: 'all',
        loading: false,
        error: '',

        async init() {
            await Promise.all([
                this.loadTodos(),
                this.loadDifficulties(),
                this.loadTags(),
                this.loadQuickTasks()
            ]);
        },

        async loadTodos() {
            const res = await fetch('/api/todos');
            this.todos = await res.json();
        },

        async loadDifficulties() {
            const res = await fetch('/api/difficulties');
            this.difficulties = await res.json();
        },

        async loadTags() {
            const res = await fetch('/api/tags');
            this.tags = await res.json();
        },

        async loadQuickTasks() {
            const res = await fetch('/api/quick-tasks');
            this.quickTasks = await res.json();
        },

        applyQuickTask(quickTask) {
            this.newTodo.content = quickTask.content;
            // 難易度が設定されている場合は反映
            if (quickTask.difficulty_id) {
                this.newTodo.difficulty_id = quickTask.difficulty_id;
            }
            // タグが設定されている場合は反映
            if (quickTask.tags && quickTask.tags.length > 0) {
                this.newTodo.tag_ids = quickTask.tags.map(t => t.id);
            }
        },

        get filteredTodos() {
            if (this.filter === 'active') {
                return this.todos.filter(t => !t.completed_at);
            } else if (this.filter === 'completed') {
                return this.todos.filter(t => t.completed_at);
            }
            return this.todos;
        },

        async createTodo() {
            this.error = '';

            // バリデーション
            if (!this.newTodo.content.trim()) {
                this.error = '内容を入力してください';
                return;
            }
            if (!this.newTodo.difficulty_id) {
                this.error = '難易度を選択してください';
                return;
            }

            this.loading = true;
            try {
                const res = await fetch('/api/todos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newTodo)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                // リセット & リロード
                this.newTodo = { content: '', difficulty_id: null, tag_ids: [] };
                await this.loadTodos();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        async completeTodo(todo) {
            if (todo.completed_at) return;

            try {
                const res = await fetch(`/api/todos/${todo.id}/complete`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    alert('タスクを完了しました');
                    await this.loadTodos();
                }
            } catch (e) {
                console.error(e);
            }
        },

        async deleteTodo(todo) {
            if (!confirm('このTODOを削除しますか？')) return;

            try {
                const res = await fetch(`/api/todos/${todo.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    await this.loadTodos();
                }
            } catch (e) {
                console.error(e);
            }
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    };
}
</script>
@endsection

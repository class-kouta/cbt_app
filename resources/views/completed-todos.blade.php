@extends('layouts.app')

@section('title', '完了済みTODO一覧')

@section('content')
<div x-data="completedTodosApp()" x-init="init()" x-cloak>
    <!-- 統計サマリー -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-md p-5 text-center">
            <p class="text-gray-500 text-sm mb-1">総完了数</p>
            <p class="text-4xl font-bold text-emerald-600" x-text="todos.length">0</p>
        </div>
        <template x-for="difficulty in difficulties" :key="difficulty.id">
            <div class="bg-white rounded-xl shadow-md p-5 text-center">
                <p class="text-gray-500 text-sm mb-1" x-text="difficulty.name + '難易度'"></p>
                <p class="text-4xl font-bold" :style="{ color: difficulty.color }" x-text="countByDifficulty(difficulty.id)">0</p>
            </div>
        </template>
    </div>

    <!-- フィルター -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <!-- タグフィルター -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">タグ:</label>
                <select x-model="selectedTag" class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">すべて</option>
                    <template x-for="tag in tags" :key="tag.id">
                        <option :value="tag.id" x-text="tag.name"></option>
                    </template>
                </select>
            </div>
            <!-- 難易度フィルター -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">難易度:</label>
                <select x-model="selectedDifficulty" class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">すべて</option>
                    <template x-for="difficulty in difficulties" :key="difficulty.id">
                        <option :value="difficulty.id" x-text="difficulty.name"></option>
                    </template>
                </select>
            </div>
            <!-- 期間フィルター -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">期間:</label>
                <select x-model="selectedPeriod" class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">すべて</option>
                    <option value="today">今日</option>
                    <option value="week">今週</option>
                    <option value="month">今月</option>
                </select>
            </div>
            <div class="ml-auto text-sm text-gray-600">
                表示: <span x-text="filteredTodos.length" class="font-bold text-emerald-600"></span> 件
            </div>
        </div>
    </div>

    <!-- 完了TODO一覧 -->
    <div class="space-y-4">
        <template x-for="todo in filteredTodos" :key="todo.id">
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 hover:shadow-lg transition-shadow"
                 :style="{ 'border-left-color': todo.difficulty?.color || '#10b981' }">
                <div class="flex items-start gap-4">
                    <!-- 完了マーク -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-xl">
                            ✓
                        </div>
                    </div>

                    <!-- 内容 -->
                    <div class="flex-1 min-w-0">
                        <p class="text-gray-800 text-lg break-words overflow-wrap-anywhere" x-text="todo.content"></p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <!-- 難易度バッジ -->
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white"
                                :style="{ 'background-color': todo.difficulty?.color || '#999' }"
                            >
                                <span x-text="todo.difficulty?.name || '不明'"></span>
                                <span class="ml-1 opacity-80" x-text="todo.difficulty?.points + 'pt'"></span>
                            </span>
                            <!-- タグ -->
                            <template x-for="tag in todo.tags" :key="tag.id">
                                <span class="inline-block px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-600 border border-gray-200" x-text="tag.name"></span>
                            </template>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-400 mt-3">
                            <span class="flex items-center gap-1">
                                📅 <span x-text="formatDate(todo.created_at)"></span>
                            </span>
                            <span class="flex items-center gap-1 text-emerald-600">
                                ✅ <span x-text="formatDate(todo.completed_at)"></span>
                            </span>
                        </div>
                    </div>

                    <!-- 元に戻すボタン -->
                    <div class="flex-shrink-0">
                        <button
                            @click="uncompleteTodo(todo.id)"
                            class="text-2xl hover:scale-125 transition-transform cursor-pointer"
                            title="未完了に戻す"
                        >
                            ↩️
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- 空の状態 -->
        <div x-show="filteredTodos.length === 0 && !loading" class="text-center py-16">
            <p class="text-6xl mb-4">🌟</p>
            <p class="text-gray-500 text-lg">完了したTODOがありません</p>
            <p class="text-gray-400 text-sm mt-2">まずはTODOを作成して、達成していこう！</p>
            <a href="/todos" class="inline-block mt-6 bg-emerald-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-emerald-600 transition-colors">
                TODO作成ページへ
            </a>
        </div>

        <!-- ローディング -->
        <div x-show="loading" class="text-center py-12 text-gray-500">
            <p class="text-4xl mb-4 animate-spin inline-block">⏳</p>
            <p>読み込み中...</p>
        </div>
    </div>

    <!-- 新規作成ボタン（フローティング） -->
    <a
        href="/todos"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center text-2xl hover:from-indigo-600 hover:to-purple-600 transition-all"
        title="新しいTODOを作成"
    >
        ＋
    </a>
</div>

<script>
function completedTodosApp() {
    return {
        todos: [],
        difficulties: [],
        tags: [],
        selectedTag: '',
        selectedDifficulty: '',
        selectedPeriod: '',
        loading: true,

        async init() {
            await Promise.all([
                this.loadCompletedTodos(),
                this.loadDifficulties(),
                this.loadTags()
            ]);
            this.loading = false;
        },

        async loadCompletedTodos() {
            const res = await fetch('/api/todos/completed');
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

        get filteredTodos() {
            return this.todos.filter(todo => {
                // タグフィルター
                if (this.selectedTag && !todo.tags.some(t => t.id == this.selectedTag)) {
                    return false;
                }
                // 難易度フィルター
                if (this.selectedDifficulty && todo.difficulty_id != this.selectedDifficulty) {
                    return false;
                }
                // 期間フィルター
                if (this.selectedPeriod) {
                    const completedDate = new Date(todo.completed_at);
                    const now = new Date();
                    if (this.selectedPeriod === 'today') {
                        if (completedDate.toDateString() !== now.toDateString()) return false;
                    } else if (this.selectedPeriod === 'week') {
                        const weekAgo = new Date(now);
                        weekAgo.setDate(now.getDate() - 7);
                        if (completedDate < weekAgo) return false;
                    } else if (this.selectedPeriod === 'month') {
                        const monthAgo = new Date(now);
                        monthAgo.setMonth(now.getMonth() - 1);
                        if (completedDate < monthAgo) return false;
                    }
                }
                return true;
            });
        },

        countByDifficulty(difficultyId) {
            return this.todos.filter(t => t.difficulty_id === difficultyId).length;
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
        },

        async uncompleteTodo(todoId) {
            if (!confirm('このTODOを未完了に戻しますか？')) {
                return;
            }

            try {
                const res = await fetch(`/api/todos/${todoId}/uncomplete`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });

                if (res.ok) {
                    // 完了済みリストから削除
                    this.todos = this.todos.filter(t => t.id !== todoId);
                    // 成功メッセージ
                    alert('TODOを未完了に戻しました！');
                } else {
                    alert('エラーが発生しました');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('エラーが発生しました');
            }
        }
    };
}
</script>
@endsection

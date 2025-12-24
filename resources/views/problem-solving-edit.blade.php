@extends('layouts.app')

@section('title', '問題解決法')
@section('page-title', '問題解決法')

@section('content')
<div x-data="problemSolvingFormApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>

    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a :href="'/problem-solvings/' + itemId" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
            ← 詳細に戻る
        </a>
    </div>

    <!-- ローディング（編集モードのみ） -->
    <div x-show="loading && isEditMode" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <!-- フォーム -->
    <div x-show="!loading || !isEditMode">
        <form @submit.prevent="saveProblemSolving()">
            <div class="space-y-6">
                <!-- Step 1: 問題状況 -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="flex items-start gap-2 text-lg font-semibold text-gray-800 mb-3">
                        <span class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-500 text-white text-sm font-bold">1</span>
                        <span class="flex-1">問題状況を具体的に把握する</span>
                    </h3>
                    <p class="text-sm text-gray-500 mb-2">自分、人間関係、出来事、状況などの観点から問題を整理しましょう</p>
                    <textarea
                        x-model="form.problem_situation"
                        rows="4"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="例：仕事の締め切りが重なって、どれから手をつけていいかわからない"
                        maxlength="5000"
                        required
                    ></textarea>
                    <div class="text-xs text-gray-400 text-right" x-text="form.problem_situation.length + '/5000'"></div>
                </div>

                <!-- Step 2: 改善イメージ -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="flex items-start gap-2 text-lg font-semibold text-gray-800 mb-3">
                        <span class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-500 text-white text-sm font-bold">2</span>
                        <span class="flex-1">改善イメージ</span>
                    </h3>
                    <textarea
                        x-model="form.improved_image"
                        rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="例：優先順位をつけて、一つずつ片付けている。焦らず落ち着いて取り組めている。"
                        maxlength="2000"
                    ></textarea>
                </div>

                <!-- Step 3: 解決策 -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="flex items-start gap-2 text-lg font-semibold text-gray-800 mb-3">
                        <span class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-500 text-white text-sm font-bold">3</span>
                        <span class="flex-1">解決策</span>
                    </h3>
                    <p class="text-sm text-gray-500 mb-3">解決策を複数出して、それぞれの効果と実行可能性を評価しましょう（保存後に追加できます）</p>
                    <div class="space-y-3">
                        <template x-for="(solution, index) in solutions" :key="index">
                            <div class="border border-gray-300 rounded-lg p-3">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-500 font-medium" x-text="'解決策 ' + (index + 1)"></span>
                                        <span
                                            class="text-xs"
                                            :class="solution.content.length > 30 ? 'text-red-500 font-semibold' : 'text-gray-400'"
                                            x-text="solution.content.length + '/30'"
                                        ></span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeSolution(index)"
                                        class="text-red-400 hover:text-red-600 transition-colors p-1 rounded hover:bg-red-50"
                                        title="削除"
                                    >
                                        🗑️
                                    </button>
                                </div>
                                <textarea
                                    x-model="solution.content"
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent mb-3 resize-none"
                                    :class="solution.content.length > 30 ? 'border-red-500 focus:ring-red-500' : ''"
                                    placeholder="解決策を入力（30文字以内）"
                                ></textarea>
                                <div x-show="solution.content.length > 30" class="text-xs text-red-500 mb-2">
                                    30文字以内で入力してください
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">効果的か</label>
                                        <select
                                            x-model="solution.effectiveness"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                        >
                                            <option value="">選択してください</option>
                                            <option value="0">0%</option>
                                            <option value="5">5%</option>
                                            <option value="10">10%</option>
                                            <option value="15">15%</option>
                                            <option value="20">20%</option>
                                            <option value="25">25%</option>
                                            <option value="30">30%</option>
                                            <option value="35">35%</option>
                                            <option value="40">40%</option>
                                            <option value="45">45%</option>
                                            <option value="50">50%</option>
                                            <option value="55">55%</option>
                                            <option value="60">60%</option>
                                            <option value="65">65%</option>
                                            <option value="70">70%</option>
                                            <option value="75">75%</option>
                                            <option value="80">80%</option>
                                            <option value="85">85%</option>
                                            <option value="90">90%</option>
                                            <option value="95">95%</option>
                                            <option value="100">100%</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">実行可能か</label>
                                        <select
                                            x-model="solution.feasibility"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                        >
                                            <option value="">選択してください</option>
                                            <option value="0">0%</option>
                                            <option value="5">5%</option>
                                            <option value="10">10%</option>
                                            <option value="15">15%</option>
                                            <option value="20">20%</option>
                                            <option value="25">25%</option>
                                            <option value="30">30%</option>
                                            <option value="35">35%</option>
                                            <option value="40">40%</option>
                                            <option value="45">45%</option>
                                            <option value="50">50%</option>
                                            <option value="55">55%</option>
                                            <option value="60">60%</option>
                                            <option value="65">65%</option>
                                            <option value="70">70%</option>
                                            <option value="75">75%</option>
                                            <option value="80">80%</option>
                                            <option value="85">85%</option>
                                            <option value="90">90%</option>
                                            <option value="95">95%</option>
                                            <option value="100">100%</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button
                        type="button"
                        @click="addSolutionRow()"
                        x-show="solutions.length < 7"
                        class="mt-2 text-sm text-emerald-600 hover:text-emerald-800 flex items-center gap-1"
                    >
                        <span>＋</span> 解決策を追加
                    </button>
                </div>

                <!-- Step 4: 実行計画 -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="flex items-start gap-2 text-lg font-semibold text-gray-800 mb-3">
                        <span class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-500 text-white text-sm font-bold">4</span>
                        <span class="flex-1">実行計画</span>
                    </h3>
                    <p class="text-sm text-gray-500 mb-2">いつ・どこで・どんなとき・誰と・何をどうする・妨げる要因と対策・検証方法</p>
                    <textarea
                        x-model="form.action_plan"
                        rows="4"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="例：明日の朝9時に、まず締め切りが近いものをリストアップする。..."
                        maxlength="5000"
                    ></textarea>
                </div>

                <!-- Step 5: 振り返り -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="flex items-start gap-2 text-lg font-semibold text-gray-800 mb-3">
                        <span class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-500 text-white text-sm font-bold">5</span>
                        <span class="flex-1">振り返り</span>
                    </h3>
                    <p class="text-sm text-gray-500 mb-2">実行後に記入：結果、うまくいったこと、改善点、学んだこと、次に活かせること</p>
                    <textarea
                        x-model="form.reflection"
                        rows="4"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="（実行後に記入してください）"
                        maxlength="5000"
                    ></textarea>
                </div>

                <!-- 保存ボタン -->
                <button
                    type="submit"
                    :disabled="submitting || !form.problem_situation.trim()"
                    class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-3 px-6 rounded-lg font-medium hover:from-emerald-600 hover:to-teal-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span x-show="!submitting && !isEditMode">保存する</span>
                    <span x-show="!submitting && isEditMode">更新する</span>
                    <span x-show="submitting && !isEditMode">保存中...</span>
                    <span x-show="submitting && isEditMode">更新中...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 成功メッセージ（編集時のみ） -->
    <div
        x-show="showSuccess && isEditMode"
        x-transition
        class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg"
    >
        更新しました！
    </div>
</div>

<script>
function problemSolvingFormApp(itemId) {
    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        form: {
            problem_situation: '',
            improved_image: '',
            action_plan: '',
            reflection: ''
        },
        solutions: [
            { content: '', effectiveness: '', feasibility: '' },
            { content: '', effectiveness: '', feasibility: '' },
            { content: '', effectiveness: '', feasibility: '' }
        ],
        originalSolutions: [],
        loading: false,
        submitting: false,
        showSuccess: false,

        async init() {
            if (this.isEditMode) {
                await this.loadItem();
            }
        },

        async loadItem() {
            this.loading = true;
            try {
                const res = await fetch(`/api/problem-solvings/${this.itemId}`);
                if (res.ok) {
                    const item = await res.json();
                    this.form.problem_situation = item.problem_situation || '';
                    this.form.improved_image = item.improved_image || '';
                    this.form.action_plan = item.action_plan || '';
                    this.form.reflection = item.reflection || '';

                    this.solutions = item.solutions.map(s => ({
                        id: s.id,
                        content: s.content,
                        effectiveness: s.effectiveness,
                        feasibility: s.feasibility,
                        sort_order: s.sort_order
                    }));
                    this.originalSolutions = JSON.parse(JSON.stringify(this.solutions));

                    // 最低3行表示
                    while (this.solutions.length < 3) {
                        this.solutions.push({ id: null, content: '', effectiveness: '', feasibility: '' });
                    }
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        addSolutionRow() {
            if (this.solutions.length < 7) {
                this.solutions.push({ id: null, content: '', effectiveness: '', feasibility: '' });
            }
        },

        removeSolution(index) {
            this.solutions.splice(index, 1);
        },

        async saveProblemSolving() {
            if (this.isEditMode) {
                await this.updateProblemSolving();
            } else {
                await this.createProblemSolving();
            }
        },

        async createProblemSolving() {
            if (this.submitting || !this.form.problem_situation.trim()) return;

            // 解決策の文字数バリデーション
            const validSolutions = this.solutions.filter(s => s.content.trim());
            for (const solution of validSolutions) {
                if (solution.content.length > 30) {
                    alert('解決策は30文字以内で入力してください');
                    return;
                }
            }

            this.submitting = true;

            try {
                // まず問題解決を作成
                const res = await fetch('/api/problem-solvings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                if (!res.ok) throw new Error('Failed to create');

                const created = await res.json();

                // 解決策を追加（バリデーション済み）
                for (let i = 0; i < validSolutions.length; i++) {
                    const solution = validSolutions[i];
                    await fetch(`/api/problem-solvings/${created.id}/solutions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            content: solution.content,
                            sort_order: i + 1,
                            effectiveness: solution.effectiveness ? parseInt(solution.effectiveness) : null,
                            feasibility: solution.feasibility ? parseInt(solution.feasibility) : null
                        })
                    });
                }

                window.location.href = `/problem-solvings/${created.id}`;

            } catch (error) {
                console.error(error);
                alert('保存に失敗しました');
            } finally {
                this.submitting = false;
            }
        },

        async updateProblemSolving() {
            if (this.submitting || !this.form.problem_situation.trim()) return;

            // 解決策の文字数バリデーション
            const validSolutions = this.solutions.filter(s => s.content.trim());
            for (const solution of validSolutions) {
                if (solution.content.length > 30) {
                    alert('解決策は30文字以内で入力してください');
                    return;
                }
            }

            this.submitting = true;

            try {
                // 問題解決を更新
                const res = await fetch(`/api/problem-solvings/${this.itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                if (!res.ok) throw new Error('Failed to update');

                // 既存の解決策を削除
                for (const original of this.originalSolutions) {
                    await fetch(`/api/problem-solvings/${this.itemId}/solutions/${original.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                }

                // 新しい解決策を追加（バリデーション済み）
                for (let i = 0; i < validSolutions.length; i++) {
                    const solution = validSolutions[i];
                    await fetch(`/api/problem-solvings/${this.itemId}/solutions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            content: solution.content,
                            sort_order: i + 1,
                            effectiveness: solution.effectiveness ? parseInt(solution.effectiveness) : null,
                            feasibility: solution.feasibility ? parseInt(solution.feasibility) : null
                        })
                    });
                }

                this.showSuccess = true;
                setTimeout(() => {
                    this.showSuccess = false;
                    window.location.href = `/problem-solvings/${this.itemId}`;
                }, 1000);

            } catch (error) {
                console.error(error);
                alert('更新に失敗しました');
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection

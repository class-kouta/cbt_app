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

    <!-- ストレッサーとストレス反応から転記ボタン（データがある場合のみ表示） -->
    <div x-show="stressorAndResponses.length > 0 && !isEditMode" class="mb-4">
        <button
            type="button"
            @click="showStressorModal = true"
            class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-3 px-4 rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-600 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            ストレッサーとストレス反応から転記する
        </button>
    </div>

    <!-- ストレッサーとストレス反応選択モーダル -->
    <div
        x-show="showStressorModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="showStressorModal = false"
    >
        <!-- オーバーレイ -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showStressorModal = false"></div>

        <!-- モーダルコンテンツ -->
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl"
                @click.stop
            >
                <!-- ヘッダー -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            転記元を選択
                        </h3>
                        <button
                            type="button"
                            @click="showStressorModal = false"
                            class="text-white hover:text-indigo-100 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-indigo-100 mt-1">
                        選択すると「問題状況を具体的に把握する」に転記されます
                    </p>
                </div>

                <!-- コンテンツ -->
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="item in stressorAndResponses" :key="item.id">
                        <div
                            @click="applyStressorData(item)"
                            class="px-6 py-4 border-b border-gray-100 hover:bg-indigo-50 cursor-pointer transition-colors group"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <!-- ストレッサー（問題状況に転記） -->
                                    <div class="mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 mb-1">
                                            問題状況へ
                                        </span>
                                        <p class="text-sm text-gray-800 line-clamp-2" x-text="item.stressor"></p>
                                    </div>
                                    <!-- 作成日時 -->
                                    <p class="text-xs text-gray-400 mt-2" x-text="formatDate(item.created_at)"></p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 group-hover:bg-indigo-200 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- フッター -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button
                        type="button"
                        @click="showStressorModal = false"
                        class="w-full py-2.5 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                    >
                        キャンセル
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 転記成功トースト -->
    <div
        x-show="showTransferToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2"
    >
        <span>📝</span>
        <span>転記しました！</span>
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
            <div class="space-y-5">
                <!-- Step 1: 問題状況 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">1</span>
                        問題状況を具体的に把握する <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal ml-1">自分、人間関係、出来事、状況などの観点から問題を整理</span>
                    </label>
                    <textarea
                        x-model="form.problem_situation"
                        rows="10"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="例：仕事の締め切りが重なって、どれから手をつけていいかわからない"
                        maxlength="5000"
                        required
                    ></textarea>
                    <div class="text-xs text-gray-400 text-right" x-text="form.problem_situation.length + '/5000'"></div>
                </div>

                <!-- Step 2: 改善イメージ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">2</span>
                        改善イメージ
                    </label>
                    <textarea
                        x-model="form.improved_image"
                        rows="10"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="例：優先順位をつけて、一つずつ片付けている。焦らず落ち着いて取り組めている。"
                        maxlength="2000"
                    ></textarea>
                </div>

                <!-- Step 3: 解決策 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">3</span>
                        解決策
                        <span class="text-gray-400 font-normal ml-1">解決策を複数出して、それぞれの効果と実行可能性を評価しましょう</span>
                    </label>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">4</span>
                        実行計画
                        <span class="text-gray-400 font-normal ml-1">いつ・どこで・どんなとき・誰と・何をどうする・妨げる要因と対策・検証方法</span>
                    </label>
                    <textarea
                        x-model="form.action_plan"
                        rows="10"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="例：明日の朝9時に、まず締め切りが近いものをリストアップする。..."
                        maxlength="5000"
                    ></textarea>
                </div>

                <!-- Step 5: 振り返り -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">5</span>
                        振り返り
                        <span class="text-gray-400 font-normal ml-1">実行後に記入：結果、うまくいったこと、改善点、学んだこと、次に活かせること</span>
                    </label>
                    <textarea
                        x-model="form.reflection"
                        rows="10"
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

        // 自動保存用
        autoSaveSnapshots: [],
        autoSaveInterval: null,
        autoSaving: false,
        showAutoSaveToast: false,

        // ストレッサーとストレス反応からの転記機能
        stressorAndResponses: [],
        showStressorModal: false,
        showTransferToast: false,

        async init() {
            // ストレッサーとストレス反応一覧を取得
            await this.loadStressorAndResponses();

            if (this.isEditMode) {
                await this.loadItem();
            }

            // 初期スナップショットを取得
            this.takeSnapshot();

            // 30秒ごとに自動保存チェック
            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        // ストレッサーとストレス反応一覧を取得
        async loadStressorAndResponses() {
            try {
                const res = await fetch('/api/stressor-and-responses');
                if (res.ok) {
                    this.stressorAndResponses = await res.json();
                }
            } catch (error) {
                console.error('ストレッサーとストレス反応の取得に失敗しました:', error);
            }
        },

        // ストレッサーとストレス反応のデータを転記
        applyStressorData(item) {
            // 問題状況 ← ストレッサー
            this.form.problem_situation = item.stressor || '';

            // モーダルを閉じる
            this.showStressorModal = false;

            // 転記成功トーストを表示
            this.showTransferToast = true;
            setTimeout(() => {
                this.showTransferToast = false;
            }, 2000);
        },

        // 現在の値のスナップショットを取得
        takeSnapshot() {
            const snapshot = {
                problem_situation: this.form.problem_situation,
                improved_image: this.form.improved_image,
                action_plan: this.form.action_plan,
                reflection: this.form.reflection,
                solutions: JSON.stringify(this.solutions)
            };
            this.autoSaveSnapshots.push(snapshot);

            // 直近2つ分のみ保持（60秒前の値と比較するため）
            if (this.autoSaveSnapshots.length > 2) {
                this.autoSaveSnapshots.shift();
            }
        },

        // 60秒前のスナップショットと現在の値を比較
        hasChangedFromPreviousSnapshot() {
            // 2回分のスナップショットがない場合（まだ60秒経っていない）
            if (this.autoSaveSnapshots.length < 2) {
                // 1つ目のスナップショットと比較
                if (this.autoSaveSnapshots.length === 1) {
                    return this.hasValueChanged(this.autoSaveSnapshots[0]);
                }
                return false;
            }

            // 60秒前（2回前）のスナップショットと比較
            const oldSnapshot = this.autoSaveSnapshots[0];
            return this.hasValueChanged(oldSnapshot);
        },

        // 指定されたスナップショットと現在の値を比較
        hasValueChanged(snapshot) {
            return (
                this.form.problem_situation !== snapshot.problem_situation ||
                this.form.improved_image !== snapshot.improved_image ||
                this.form.action_plan !== snapshot.action_plan ||
                this.form.reflection !== snapshot.reflection ||
                JSON.stringify(this.solutions) !== snapshot.solutions
            );
        },

        // 30秒ごとの自動保存チェック
        async checkAndAutoSave() {
            // 条件チェック：
            // 1. 「問題状況」が入力済み
            // 2. 1分前の値から変更がある
            // 3. 現在保存中でない
            if (
                this.form.problem_situation.trim() &&
                this.hasChangedFromPreviousSnapshot() &&
                !this.submitting &&
                !this.autoSaving
            ) {
                await this.performAutoSave();
            }

            // 新しいスナップショットを取得
            this.takeSnapshot();
        },

        // 自動保存を実行
        async performAutoSave() {
            // 解決策の文字数バリデーション
            const validSolutions = this.solutions.filter(s => s.content.trim());
            for (const solution of validSolutions) {
                if (solution.content.length > 30) {
                    // バリデーションエラーの場合は自動保存しない
                    return;
                }
            }

            this.autoSaving = true;

            try {
                if (this.itemId) {
                    // 既存の問題解決法を更新
                    const res = await fetch(`/api/problem-solvings/${this.itemId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });

                    if (res.ok) {
                        // 既存の解決策を削除して新しいのを追加
                        for (const original of this.originalSolutions) {
                            await fetch(`/api/problem-solvings/${this.itemId}/solutions/${original.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                        }

                        // 新しい解決策を追加
                        const newOriginalSolutions = [];
                        for (let i = 0; i < validSolutions.length; i++) {
                            const solution = validSolutions[i];
                            const solutionRes = await fetch(`/api/problem-solvings/${this.itemId}/solutions`, {
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
                            if (solutionRes.ok) {
                                const createdSolution = await solutionRes.json();
                                newOriginalSolutions.push(createdSolution);
                            }
                        }
                        this.originalSolutions = newOriginalSolutions;
                        this.showAutoSaveNotification();
                    }
                } else {
                    // 新規作成
                    const res = await fetch('/api/problem-solvings', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });

                    if (res.ok) {
                        const created = await res.json();
                        this.itemId = created.id;
                        this.isEditMode = true;

                        // 解決策を追加
                        const newOriginalSolutions = [];
                        for (let i = 0; i < validSolutions.length; i++) {
                            const solution = validSolutions[i];
                            const solutionRes = await fetch(`/api/problem-solvings/${created.id}/solutions`, {
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
                            if (solutionRes.ok) {
                                const createdSolution = await solutionRes.json();
                                newOriginalSolutions.push(createdSolution);
                            }
                        }
                        this.originalSolutions = newOriginalSolutions;

                        // URLを編集ページに変更（リロードなし）
                        history.replaceState(null, '', `/problem-solvings/${created.id}/edit`);
                        this.showAutoSaveNotification();
                    }
                }
            } catch (error) {
                console.error('自動保存に失敗しました:', error);
            } finally {
                this.autoSaving = false;
            }
        },

        // 自動保存の通知を表示
        showAutoSaveNotification() {
            this.showAutoSaveToast = true;
            setTimeout(() => {
                this.showAutoSaveToast = false;
            }, 2000);
        },

        // 日時をフォーマット
        formatDate(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}/${month}/${day} ${hours}:${minutes}`;
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

                // 更新成功したら詳細ページに遷移
                window.location.href = `/problem-solvings/${this.itemId}`;

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

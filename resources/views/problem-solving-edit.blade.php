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
        class="fixed top-16 right-4 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        保存しました
    </div>

    <!-- フローティング保存ボタン -->
    <button
        type="button"
        @click="manualSave()"
        :disabled="floatingSaving || !form.problem_situation.trim()"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center hover:from-emerald-600 hover:to-teal-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed z-30"
        title="保存する"
    >
        <template x-if="!floatingSaving">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zM16 20v-6H8v6M8 4v4h6"></path>
            </svg>
        </template>
        <template x-if="floatingSaving">
            <svg class="animate-spin w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </template>
    </button>

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

                <!-- タグセクション -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        🏷️ タグ
                        <span class="text-gray-400 font-normal text-sm">（任意・複数選択可）</span>
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">
                        この問題に関連するカテゴリーを選択してください
                    </p>

                    <!-- 選択されたタグ表示 -->
                    <div x-show="form.tag_ids.length > 0" class="mb-3">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="tagId in form.tag_ids" :key="tagId">
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">
                                    <span x-text="getTagName(tagId)"></span>
                                    <button
                                        type="button"
                                        @click="toggleTag(tagId)"
                                        class="ml-1 text-emerald-500 hover:text-emerald-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- タグ選択UI -->
                    <div class="flex flex-wrap gap-2">
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
                                                :class="solution.content.length > 100 ? 'text-red-500 font-semibold' : 'text-gray-400'"
                                                x-text="solution.content.length + '/100'"
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
                                    :class="solution.content.length > 100 ? 'border-red-500 focus:ring-red-500' : ''"
                                    placeholder="解決策を入力（100文字以内）"
                                ></textarea>
                                <div x-show="solution.content.length > 100" class="text-xs text-red-500 mb-2">
                                    100文字以内で入力してください
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

                <!-- Step 4 & 5: 実行計画と振り返り -->
                <div class="border-t border-gray-200 pt-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <span x-show="isEditMode">実行計画と振り返り</span>
                            <span x-show="!isEditMode">実行計画</span>
                        </h3>
                    </div>

                    <!-- 計画がない場合の説明 -->
                    <div x-show="plans.length === 0" class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-gray-600 text-sm" x-show="isEditMode">
                            解決策を決めたら、実行計画を立てましょう。計画を実行した後に振り返りを記入し、必要に応じて新しい計画を追加できます。
                        </p>
                        <p class="text-gray-600 text-sm" x-show="!isEditMode">
                            解決策を決めたら、実行計画を立てましょう。複数の計画を追加することもできます。
                        </p>
                    </div>

                    <!-- 計画一覧 -->
                    <div class="space-y-4">
                        <template x-for="(plan, index) in plans" :key="plan.id || 'new-' + index">
                            <div class="border border-teal-200 rounded-xl overflow-hidden bg-white shadow-sm">
                                <!-- 計画ヘッダー -->
                                <div
                                    class="bg-gradient-to-r from-teal-50 to-lime-50 px-4 py-3 flex items-center justify-between cursor-pointer"
                                    @click="plan.expanded = !plan.expanded"
                                >
                                    <div class="flex items-center gap-3">
                                        <!-- ステータスバッジ -->
                                        <span
                                            x-show="isEditMode && plan.reflection && plan.reflection.trim()"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"
                                        >
                                            ✓ 振り返り済み
                                        </span>
                                        <span
                                            x-show="plan.action_plan && plan.action_plan.trim() && (!plan.reflection || !plan.reflection.trim())"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700"
                                        >
                                            実行中
                                        </span>
                                        <span
                                            x-show="!plan.action_plan || !plan.action_plan.trim()"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"
                                        >
                                            計画作成中
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click.stop="deletePlan(index)"
                                            x-show="plans.length > 1 || (plans.length === 1 && !plan.id)"
                                            class="text-red-400 hover:text-red-600 transition-colors p-1 rounded hover:bg-red-50"
                                            title="この計画を削除"
                                        >
                                            🗑️
                                        </button>
                                        <svg
                                            class="w-5 h-5 text-gray-400 transition-transform"
                                            :class="plan.expanded ? 'rotate-180' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- 計画コンテンツ -->
                                <div x-show="plan.expanded" x-collapse class="px-4 py-4 space-y-4">
                                    <!-- 実行計画 -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-teal-500 text-white text-xs mr-1">4</span>
                                            実行計画
                                            <span class="text-gray-400 font-normal ml-1">いつ・どこで・どんなとき・誰と・何をどうする・妨げる要因と対策・検証方法</span>
                                        </label>
                                        <textarea
                                            x-model="plan.action_plan"
                                            rows="8"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                                            placeholder="例：明日の朝9時に、まず締め切りが近いものをリストアップする。..."
                                            maxlength="5000"
                                        ></textarea>
                                    </div>

                                    <!-- 振り返り（編集モードのみ表示） -->
                                    <div x-show="isEditMode">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-lime-500 text-white text-xs mr-1">5</span>
                                            振り返り
                                            <span class="text-gray-400 font-normal ml-1">実行後に記入：結果、うまくいったこと、改善点、学んだこと、次に活かせること</span>
                                        </label>
                                        <textarea
                                            x-model="plan.reflection"
                                            rows="8"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all"
                                            placeholder="（実行後に記入してください）"
                                            maxlength="5000"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- 新しい計画を追加ボタン -->
                    <div class="mt-4">
                        <button
                            type="button"
                            @click="addNewPlan()"
                            class="w-full py-3 px-4 border-2 border-dashed rounded-xl font-medium transition-all flex items-center justify-center gap-2 border-teal-300 text-teal-600 hover:border-teal-400 hover:bg-teal-50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            新しい計画を追加
                        </button>
                    </div>
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
            tag_ids: []
        },
        solutions: [
            { content: '', effectiveness: '', feasibility: '' },
            { content: '', effectiveness: '', feasibility: '' },
            { content: '', effectiveness: '', feasibility: '' }
        ],
        originalSolutions: [],
        plans: [],
        originalPlans: [],
        loading: false,
        submitting: false,
        showManualSaveToast: false,
        floatingSaving: false,

        // タグ一覧
        availableTags: [],

        // 自動保存用
        autoSaveSnapshots: [],
        autoSaveInterval: null,
        autoSaving: false,
        showAutoSaveToast: false,

        async init() {
            // タグ一覧を取得
            await this.loadTags();

            if (this.isEditMode) {
                await this.loadItem();
            } else {
                // 新規作成の場合は空の計画を1つ追加
                this.plans = [{ id: null, plan_number: 1, action_plan: '', reflection: '', expanded: true }];
            }

            // 初期スナップショットを取得
            this.takeSnapshot();

            // 30秒ごとに自動保存チェック
            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        async loadTags() {
            try {
                const res = await fetch('/api/tags');
                if (res.ok) {
                    this.availableTags = await res.json();
                }
            } catch (error) {
                console.error('タグの取得に失敗しました:', error);
            }
        },

        toggleTag(tagId) {
            const index = this.form.tag_ids.indexOf(tagId);
            if (index > -1) {
                this.form.tag_ids.splice(index, 1);
            } else {
                this.form.tag_ids.push(tagId);
            }
        },

        isTagSelected(tagId) {
            return this.form.tag_ids.includes(tagId);
        },

        getTagName(tagId) {
            const tag = this.availableTags.find(t => t.id === tagId);
            return tag ? tag.name : '';
        },

        // 現在の値のスナップショットを取得
        takeSnapshot() {
            const snapshot = {
                problem_situation: this.form.problem_situation,
                improved_image: this.form.improved_image,
                solutions: JSON.stringify(this.solutions),
                plans: JSON.stringify(this.plans.map(p => ({ action_plan: p.action_plan, reflection: p.reflection }))),
                tag_ids: JSON.stringify(this.form.tag_ids)
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
            const currentPlans = JSON.stringify(this.plans.map(p => ({ action_plan: p.action_plan, reflection: p.reflection })));
            return (
                this.form.problem_situation !== snapshot.problem_situation ||
                this.form.improved_image !== snapshot.improved_image ||
                JSON.stringify(this.solutions) !== snapshot.solutions ||
                currentPlans !== snapshot.plans ||
                JSON.stringify(this.form.tag_ids) !== snapshot.tag_ids
            );
        },

        // 30秒ごとの自動保存チェック
        async checkAndAutoSave() {
            if (
                this.form.problem_situation.trim() &&
                this.hasChangedFromPreviousSnapshot() &&
                !this.submitting &&
                !this.autoSaving
            ) {
                await this.performAutoSave();
            }

            this.takeSnapshot();
        },

        // 解決策のバリデーション
        validateSolutions() {
            const validSolutions = this.solutions.filter(s => s.content.trim());
            for (const solution of validSolutions) {
                if (solution.content.length > 100) {
                    return false;
                }
            }
            return true;
        },

        // 共通の保存処理
        async performSave(isManual = false) {
            if (!this.validateSolutions()) {
                return;
            }

            try {
                if (this.itemId) {
                    await this.saveExistingItem();
                } else {
                    await this.saveNewItem();
                }
                this.showSaveNotification(isManual);
            } catch (error) {
                console.error(isManual ? '保存に失敗しました:' : '自動保存に失敗しました:', error);
            }
        },

        // 自動保存を実行
        async performAutoSave() {
            this.autoSaving = true;
            try {
                await this.performSave(false);
            } finally {
                this.autoSaving = false;
            }
        },

        // 手動保存（フローティングボタン用）
        async manualSave() {
            if (this.floatingSaving || !this.form.problem_situation.trim()) return;

            this.floatingSaving = true;
            try {
                await this.performSave(true);
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
                    this.form.tag_ids = item.tag_ids || [];

                    this.solutions = item.solutions.map(s => ({
                        id: s.id,
                        content: s.content,
                        effectiveness: s.effectiveness,
                        feasibility: s.feasibility,
                        sort_order: s.sort_order
                    }));
                    this.originalSolutions = JSON.parse(JSON.stringify(this.solutions));

                    // 計画を読み込み
                    if (item.plans && item.plans.length > 0) {
                        this.plans = item.plans.map(p => ({
                            id: p.id,
                            plan_number: p.plan_number,
                            action_plan: p.action_plan || '',
                            reflection: p.reflection || '',
                            expanded: true
                        }));
                    } else {
                        this.plans = [{ id: null, plan_number: 1, action_plan: '', reflection: '', expanded: true }];
                    }
                    this.originalPlans = JSON.parse(JSON.stringify(this.plans));

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

        addNewPlan() {
            const nextNumber = this.plans.length > 0
                ? Math.max(...this.plans.map(p => p.plan_number)) + 1
                : 1;

            this.plans.push({
                id: null,
                plan_number: nextNumber,
                action_plan: '',
                reflection: '',
                expanded: true
            });
        },

        deletePlan(index) {
            if (this.plans.length <= 1 && this.plans[index].id) return;
            this.plans.splice(index, 1);
        },

        async saveProblemSolving() {
            if (this.isEditMode) {
                await this.updateProblemSolving();
            } else {
                await this.createProblemSolving();
            }
        },

        async saveNewItem() {
            const validSolutions = this.solutions.filter(s => s.content.trim());

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
                // 注意: isEditModeは変更しない（自動保存時にUIが切り替わるのを防ぐ）

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

                // 計画を追加
                await this.savePlans(created.id);

                history.replaceState(null, '', `/problem-solvings/${created.id}/edit`);
            }
        },

        async saveExistingItem() {
            const validSolutions = this.solutions.filter(s => s.content.trim());

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

                // 計画を保存
                await this.savePlans(this.itemId);
            }
        },

        async savePlans(problemSolvingId) {
            // 既存の計画を更新、新規計画を追加
            for (const plan of this.plans) {
                if (plan.id) {
                    // 既存の計画を更新
                    await fetch(`/api/problem-solvings/${problemSolvingId}/plans/${plan.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            action_plan: plan.action_plan || null,
                            reflection: plan.reflection || null
                        })
                    });
                } else {
                    // 新規計画を追加（action_plan か reflection が入力されている場合のみ）
                    if ((plan.action_plan && plan.action_plan.trim()) || (plan.reflection && plan.reflection.trim())) {
                        const planRes = await fetch(`/api/problem-solvings/${problemSolvingId}/plans`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                action_plan: plan.action_plan || null,
                                reflection: plan.reflection || null
                            })
                        });
                        if (planRes.ok) {
                            const createdPlan = await planRes.json();
                            plan.id = createdPlan.id;
                        }
                    }
                }
            }

            // 削除された計画を処理
            for (const original of this.originalPlans) {
                if (!this.plans.find(p => p.id === original.id)) {
                    await fetch(`/api/problem-solvings/${problemSolvingId}/plans/${original.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                }
            }

            this.originalPlans = JSON.parse(JSON.stringify(this.plans));
        },

        async createProblemSolving() {
            if (this.submitting || !this.form.problem_situation.trim()) return;

            const validSolutions = this.solutions.filter(s => s.content.trim());
            for (const solution of validSolutions) {
                if (solution.content.length > 100) {
                    alert('解決策は100文字以内で入力してください');
                    return;
                }
            }

            this.submitting = true;

            try {
                await this.saveNewItem();
                window.location.href = `/problem-solvings/${this.itemId}`;
            } catch (error) {
                console.error(error);
                alert('保存に失敗しました');
            } finally {
                this.submitting = false;
            }
        },

        async updateProblemSolving() {
            if (this.submitting || !this.form.problem_situation.trim()) return;

            const validSolutions = this.solutions.filter(s => s.content.trim());
            for (const solution of validSolutions) {
                if (solution.content.length > 100) {
                    alert('解決策は100文字以内で入力してください');
                    return;
                }
            }

            this.submitting = true;

            try {
                await this.saveExistingItem();
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

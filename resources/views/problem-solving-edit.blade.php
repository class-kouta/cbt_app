@extends('layouts.app')

@section('title', '問題解決法')
@section('page-title', '問題解決法')

@section('content')
<div x-data="problemSolvingFormApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>

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

    <!-- コピー成功トースト -->
    <div
        x-show="showCopyToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2"
    >
        <span>📋</span>
        <span>コピーしました！</span>
    </div>

    <!-- フローティング保存ボタン（編集中のみ表示） -->
    <button
        x-show="isEditing"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
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

    <!-- ヘッダー -->
    <div class="flex justify-between items-center mb-4">
        <a x-show="hasExistingRecord" :href="backUrl" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
            ← <span x-text="backLabel"></span>
        </a>
        <div x-show="!hasExistingRecord"></div>
        <div class="flex items-center gap-2">
            <!-- 編集トグルボタン（既存レコードのみ） -->
            <button
                x-show="hasExistingRecord"
                type="button"
                @click="isEditing ? saveAndStopEditing() : startEditing()"
                :disabled="isEditing && (submitting || floatingSaving)"
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
                <template x-if="isEditing && !submitting && !floatingSaving">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="isEditing && (submitting || floatingSaving)">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <span x-text="isEditing ? ((submitting || floatingSaving) ? '保存中...' : '保存する') : '編集する'"></span>
            </button>
            <!-- 削除ボタン（既存レコードのみ） -->
            <button
                x-show="hasExistingRecord"
                @click="deleteItem()"
                class="text-red-400 hover:text-red-600 transition-colors p-2 rounded hover:bg-red-50"
                title="削除"
            >
                🗑️
            </button>
        </div>
    </div>

    <!-- ローディング -->
    <div x-show="loading && hasExistingRecord" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <!-- フォーム -->
    <div x-show="!loading || !hasExistingRecord">
        <form @submit.prevent="saveProblemSolving()">
            <div class="space-y-5">
                <!-- Step 1: 問題状況 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">1</span>
                        問題状況を具体的に把握する <span x-show="isEditing" class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal ml-1">自分、人間関係、出来事、状況などの観点から問題を整理</span>
                    </label>
                    <textarea
                        x-model="form.problem_situation"
                        rows="10"
                        :disabled="!isEditing"
                        class="w-full border rounded-lg px-4 py-3 transition-all"
                        :class="isEditing
                            ? 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white'
                            : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                        placeholder="例：仕事の締め切りが重なって、どれから手をつけていいかわからない"
                        maxlength="5000"
                        :required="isEditing"
                    ></textarea>
                    <div x-show="isEditing" class="text-xs text-gray-400 text-right" x-text="form.problem_situation.length + '/5000'"></div>
                </div>

                <!-- タグセクション -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        🏷️ タグ
                        <span x-show="isEditing" class="text-gray-400 font-normal text-sm">（任意・複数選択可）</span>
                    </h3>
                    <p x-show="isEditing" class="text-xs text-gray-500 mb-3">
                        この問題に関連するカテゴリーを選択してください
                    </p>

                    <!-- 編集中：タグ選択UI -->
                    <div x-show="isEditing" class="flex flex-wrap gap-2">
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

                    <!-- 閲覧中：選択済みタグ表示 -->
                    <div x-show="!isEditing" class="flex flex-wrap gap-2">
                        <template x-for="tag in selectedTagObjects" :key="tag.id">
                            <span class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium" x-text="tag.name"></span>
                        </template>
                        <span x-show="form.tag_ids.length === 0" class="text-gray-400 text-sm">タグなし</span>
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
                        :disabled="!isEditing"
                        class="w-full border rounded-lg px-4 py-3 transition-all"
                        :class="isEditing
                            ? 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white'
                            : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
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
                            <div class="border rounded-lg p-3" :class="isEditing ? 'border-gray-300' : 'border-gray-200 bg-gray-50'">
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-500 font-medium" x-text="'解決策 ' + (index + 1)"></span>
                                            <span
                                                x-show="isEditing"
                                                class="text-xs"
                                                :class="solution.content.length > 100 ? 'text-red-500 font-semibold' : 'text-gray-400'"
                                                x-text="solution.content.length + '/100'"
                                            ></span>
                                        </div>
                                    <button
                                        x-show="isEditing"
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
                                    :disabled="!isEditing"
                                    class="w-full border rounded-lg px-3 py-2 mb-3 resize-none transition-all"
                                    :class="isEditing
                                        ? (solution.content.length > 100 ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white')
                                        : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                                    placeholder="解決策を入力（100文字以内）"
                                ></textarea>
                                <div x-show="isEditing && solution.content.length > 100" class="text-xs text-red-500 mb-2">
                                    100文字以内で入力してください
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">効果的か</label>
                                        <select
                                            x-model="solution.effectiveness"
                                            :disabled="!isEditing"
                                            class="w-full border rounded-lg px-3 py-2 transition-all"
                                            :class="isEditing
                                                ? 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white'
                                                : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
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
                                            :disabled="!isEditing"
                                            class="w-full border rounded-lg px-3 py-2 transition-all"
                                            :class="isEditing
                                                ? 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white'
                                                : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
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
                    <!-- 閲覧中：解決策が0件の場合 -->
                    <p x-show="!isEditing && solutions.filter(s => s.content.trim()).length === 0" class="text-gray-400 mt-2">未入力</p>
                    <button
                        x-show="isEditing"
                        type="button"
                        @click="addSolutionRow()"
                        class="mt-2 text-sm text-emerald-600 hover:text-emerald-800 flex items-center gap-1"
                    >
                        <span>＋</span> 解決策を追加
                    </button>
                </div>

                <!-- Step 4 & 5: 実行計画と振り返り -->
                <div class="border-t border-gray-200 pt-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            実行計画と振り返り
                        </h3>
                    </div>

                    <!-- 計画がない場合の説明 -->
                    <div x-show="plans.length === 0" class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-gray-600 text-sm">
                            解決策を決めたら、実行計画を立てましょう。計画を実行した後に振り返りを記入し、必要に応じて新しい計画を追加できます。
                        </p>
                    </div>

                    <!-- 計画一覧 -->
                    <div class="space-y-4">
                        <template x-for="(plan, index) in plans" :key="plan.id || 'new-' + index">
                            <div
                                :id="plan.id ? 'plan-' + plan.id : null"
                                class="border border-teal-200 rounded-xl overflow-hidden bg-white shadow-sm"
                                :class="scrollTargetPlanId && plan.id && String(plan.id) === String(scrollTargetPlanId) ? 'ring-2 ring-emerald-400 ring-offset-2' : ''"
                            >
                                <!-- 計画ヘッダー -->
                                <div
                                    class="bg-gradient-to-r from-teal-50 to-lime-50 px-4 py-3 flex items-center justify-between cursor-pointer"
                                    @click="plan.expanded = !plan.expanded"
                                >
                                    <div class="flex items-center gap-3">
                                        <!-- ステータスバッジ -->
                                        <span
                                            x-show="plan.reflection && plan.reflection.trim()"
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
                                            x-show="isEditing && (plans.length > 1 || (plans.length === 1 && !plan.id))"
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
                                            :disabled="!isEditing"
                                            class="w-full border rounded-lg px-4 py-3 transition-all"
                                            :class="isEditing
                                                ? 'border-gray-300 focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-white'
                                                : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                                            placeholder="例：明日の朝9時に、まず締め切りが近いものをリストアップする。..."
                                            maxlength="5000"
                                        ></textarea>
                                    </div>

                                    <!-- 振り返り -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-lime-500 text-white text-xs mr-1">5</span>
                                            振り返り
                                            <span class="text-gray-400 font-normal ml-1">実行後に記入：結果、うまくいったこと、改善点、学んだこと、次に活かせること</span>
                                        </label>
                                        <textarea
                                            x-model="plan.reflection"
                                            rows="8"
                                            :disabled="!isEditing"
                                            class="w-full border rounded-lg px-4 py-3 transition-all"
                                            :class="isEditing
                                                ? 'border-gray-300 focus:ring-2 focus:ring-lime-500 focus:border-transparent bg-white'
                                                : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                                            placeholder="（実行後に記入してください）"
                                            maxlength="5000"
                                        ></textarea>

                                        <!-- 改善レベル -->
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                📊 改善レベル
                                                <span class="text-gray-400 font-normal ml-1">実行後の改善度合いを1〜10で評価</span>
                                            </label>
                                            <select
                                                x-model="plan.improvement_level"
                                                :disabled="!isEditing"
                                                class="w-full border rounded-lg px-4 py-3 transition-all"
                                                :class="isEditing
                                                    ? 'border-gray-300 focus:ring-2 focus:ring-lime-500 focus:border-transparent bg-white'
                                                    : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                                            >
                                                <option value="">選択してください</option>
                                                <option value="1">1 - ほとんど改善なし</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10 - 大幅に改善</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- 新しい計画を追加ボタン（編集中のみ） -->
                    <div x-show="isEditing" class="mt-4">
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

                <!-- ボタンエリア -->
                <div class="space-y-3">
                    <!-- 保存ボタン（編集中のみ） -->
                    <button
                        x-show="isEditing"
                        type="submit"
                        :disabled="submitting || !form.problem_situation.trim()"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-3 px-6 rounded-lg font-medium hover:from-emerald-600 hover:to-teal-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!submitting && !hasExistingRecord">保存する</span>
                        <span x-show="!submitting && hasExistingRecord">更新する</span>
                        <span x-show="submitting && !hasExistingRecord">保存中...</span>
                        <span x-show="submitting && hasExistingRecord">更新中...</span>
                    </button>

                    <!-- コピーボタン -->
                    <button
                        type="button"
                        @click="copyToClipboard()"
                        class="w-full bg-white border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all flex items-center justify-center gap-2"
                        :disabled="!hasAnyContent()"
                        :class="{ 'opacity-50 cursor-not-allowed': !hasAnyContent() }"
                    >
                        📋 内容をコピー
                    </button>

                    <!-- 一覧に戻るリンク（下部） -->
                    <div x-show="hasExistingRecord" class="text-center pt-2">
                        <a :href="backUrl" class="text-emerald-600 hover:text-emerald-800 flex items-center justify-center gap-1">
                            ← <span x-text="backLabel"></span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
function problemSolvingFormApp(itemId) {
    return {
        itemId: itemId,
        hasExistingRecord: itemId !== null,
        isEditing: itemId === null,
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
        loading: itemId !== null,
        submitting: false,
        showManualSaveToast: false,
        showCopyToast: false,
        floatingSaving: false,

        availableTags: [],

        autoSaveSnapshots: [],
        autoSaveInterval: null,
        autoSaving: false,
        showAutoSaveToast: false,

        fromPage: 'list',
        scrollTargetPlanId: null,

        get backUrl() {
            return this.fromPage === 'plans' ? '/problem-solvings/plans' : '/problem-solvings/list';
        },

        get backLabel() {
            return this.fromPage === 'plans' ? '計画一覧に戻る' : '問題解決法一覧に戻る';
        },

        get selectedTagObjects() {
            return this.availableTags.filter(t => this.form.tag_ids.includes(t.id));
        },

        async init() {
            const urlParams = new URLSearchParams(window.location.search);
            this.fromPage = urlParams.get('from') || 'list';
            this.scrollTargetPlanId = urlParams.get('plan_id') || null;

            if (this.hasExistingRecord && this.fromPage === 'plans') {
                this.isEditing = true;
            }

            await this.loadTags();

            if (this.hasExistingRecord) {
                await this.loadItem();
                if (this.isEditing) {
                    this.startAutoSave();
                }
                this.scrollToPlanIfNeeded();
            } else {
                this.plans = [{ id: null, plan_number: 1, action_plan: '', reflection: '', improvement_level: '', expanded: true }];
                this.startAutoSave();
            }

            this.takeSnapshot();
        },

        scrollToPlanIfNeeded() {
            if (!this.scrollTargetPlanId) return;

            this.$nextTick(() => {
                setTimeout(() => {
                    const planEl = document.getElementById('plan-' + this.scrollTargetPlanId);
                    if (planEl) {
                        planEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 100);
            });
        },

        startEditing() {
            this.isEditing = true;
            this.takeSnapshot();
            this.startAutoSave();
        },

        async saveAndStopEditing() {
            if (!this.form.problem_situation.trim()) return;
            if (!this.validateSolutions()) {
                alert('解決策は100文字以内で入力してください');
                return;
            }

            this.submitting = true;
            try {
                await this.performSave(true);
                this.stopEditing();
            } catch (error) {
                console.error('保存に失敗しました:', error);
                alert('保存に失敗しました');
            } finally {
                this.submitting = false;
            }
        },

        stopEditing() {
            this.isEditing = false;
            this.stopAutoSave();
        },

        startAutoSave() {
            this.stopAutoSave();
            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        stopAutoSave() {
            if (this.autoSaveInterval) {
                clearInterval(this.autoSaveInterval);
                this.autoSaveInterval = null;
            }
            this.autoSaveSnapshots = [];
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

        takeSnapshot() {
            const snapshot = {
                problem_situation: this.form.problem_situation,
                improved_image: this.form.improved_image,
                solutions: JSON.stringify(this.solutions),
                plans: JSON.stringify(this.plans.map(p => ({ action_plan: p.action_plan, reflection: p.reflection, improvement_level: p.improvement_level }))),
                tag_ids: JSON.stringify(this.form.tag_ids)
            };
            this.autoSaveSnapshots.push(snapshot);

            if (this.autoSaveSnapshots.length > 2) {
                this.autoSaveSnapshots.shift();
            }
        },

        hasChangedFromPreviousSnapshot() {
            if (this.autoSaveSnapshots.length < 2) {
                if (this.autoSaveSnapshots.length === 1) {
                    return this.hasValueChanged(this.autoSaveSnapshots[0]);
                }
                return false;
            }

            const oldSnapshot = this.autoSaveSnapshots[0];
            return this.hasValueChanged(oldSnapshot);
        },

        hasValueChanged(snapshot) {
            const currentPlans = JSON.stringify(this.plans.map(p => ({ action_plan: p.action_plan, reflection: p.reflection, improvement_level: p.improvement_level })));
            return (
                this.form.problem_situation !== snapshot.problem_situation ||
                this.form.improved_image !== snapshot.improved_image ||
                JSON.stringify(this.solutions) !== snapshot.solutions ||
                currentPlans !== snapshot.plans ||
                JSON.stringify(this.form.tag_ids) !== snapshot.tag_ids
            );
        },

        async checkAndAutoSave() {
            if (!this.isEditing) return;

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

        validateSolutions() {
            const validSolutions = this.solutions.filter(s => s.content.trim());
            for (const solution of validSolutions) {
                if (solution.content.length > 100) {
                    return false;
                }
            }
            return true;
        },

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

        async performAutoSave() {
            this.autoSaving = true;
            try {
                await this.performSave(false);
            } finally {
                this.autoSaving = false;
            }
        },

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

                    if (item.plans && item.plans.length > 0) {
                        this.plans = item.plans.map(p => ({
                            id: p.id,
                            plan_number: p.plan_number,
                            action_plan: p.action_plan || '',
                            reflection: p.reflection || '',
                            improvement_level: p.improvement_level !== null && p.improvement_level !== undefined ? String(p.improvement_level) : '',
                            expanded: true
                        }));
                    } else {
                        this.plans = [{ id: null, plan_number: 1, action_plan: '', reflection: '', improvement_level: '', expanded: true }];
                    }
                    this.originalPlans = JSON.parse(JSON.stringify(this.plans));

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
            this.solutions.push({ id: null, content: '', effectiveness: '', feasibility: '' });
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
                improvement_level: '',
                expanded: true
            });
        },

        deletePlan(index) {
            if (this.plans.length <= 1 && this.plans[index].id) return;
            this.plans.splice(index, 1);
        },

        async saveProblemSolving() {
            if (this.itemId) {
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
                this.hasExistingRecord = true;

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

                await this.savePlans(created.id);

                history.replaceState(null, '', `/problem-solvings/${created.id}`);
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

                await this.savePlans(this.itemId);
            }
        },

        async savePlans(problemSolvingId) {
            for (const plan of this.plans) {
                if (plan.id) {
                    await fetch(`/api/problem-solvings/${problemSolvingId}/plans/${plan.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            action_plan: plan.action_plan || null,
                            reflection: plan.reflection || null,
                            improvement_level: plan.improvement_level ? parseInt(plan.improvement_level) : null
                        })
                    });
                } else {
                    if ((plan.action_plan && plan.action_plan.trim()) || (plan.reflection && plan.reflection.trim())) {
                        const planRes = await fetch(`/api/problem-solvings/${problemSolvingId}/plans`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                action_plan: plan.action_plan || null,
                                reflection: plan.reflection || null,
                                improvement_level: plan.improvement_level ? parseInt(plan.improvement_level) : null
                            })
                        });
                        if (planRes.ok) {
                            const createdPlan = await planRes.json();
                            plan.id = createdPlan.id;
                        }
                    }
                }
            }

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
                this.stopEditing();
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
                this.stopEditing();
            } catch (error) {
                console.error(error);
                alert('更新に失敗しました');
            } finally {
                this.submitting = false;
            }
        },

        async deleteItem() {
            if (!confirm('この記録を削除しますか？')) return;

            try {
                const res = await fetch(`/api/problem-solvings/${this.itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (res.ok) {
                    window.location.href = this.backUrl;
                } else {
                    alert('削除に失敗しました');
                }
            } catch (error) {
                console.error(error);
                alert('削除に失敗しました');
            }
        },

        hasAnyContent() {
            if (this.form.problem_situation.trim()) return true;
            if (this.form.improved_image.trim()) return true;
            if (this.solutions.some(s => s.content.trim())) return true;
            if (this.plans.some(p => (p.action_plan && p.action_plan.trim()) || (p.reflection && p.reflection.trim()))) return true;
            return false;
        },

        generateCopyText() {
            const sections = [];
            sections.push('【問題解決法】');
            sections.push('');

            sections.push('■ 問題状況');
            sections.push(this.form.problem_situation.trim() || '未入力');
            sections.push('');

            sections.push('■ 改善イメージ');
            sections.push(this.form.improved_image.trim() || '未入力');
            sections.push('');

            const validSolutions = this.solutions.filter(s => s.content.trim());
            sections.push('■ 解決策');
            if (validSolutions.length > 0) {
                validSolutions.forEach((solution, index) => {
                    let solutionText = `${index + 1}. ${solution.content}`;
                    const ratings = [];
                    if (solution.effectiveness !== '' && solution.effectiveness !== null) {
                        ratings.push(`効果: ${solution.effectiveness}%`);
                    }
                    if (solution.feasibility !== '' && solution.feasibility !== null) {
                        ratings.push(`実行可能: ${solution.feasibility}%`);
                    }
                    if (ratings.length > 0) {
                        solutionText += ` （${ratings.join('、')}）`;
                    }
                    sections.push(solutionText);
                });
            } else {
                sections.push('未入力');
            }
            sections.push('');

            this.plans.forEach((plan, index) => {
                const planLabel = this.plans.length > 1 ? `■ 実行計画 ${index + 1}` : '■ 実行計画';
                sections.push(planLabel);
                sections.push((plan.action_plan && plan.action_plan.trim()) || '未入力');
                sections.push('');

                const reflectionLabel = this.plans.length > 1 ? `■ 振り返り ${index + 1}` : '■ 振り返り';
                sections.push(reflectionLabel);
                sections.push((plan.reflection && plan.reflection.trim()) || '未入力');
                sections.push('');
            });

            return sections.join('\n').trim();
        },

        async copyToClipboard() {
            const text = this.generateCopyText();

            try {
                await navigator.clipboard.writeText(text);
                this.showCopyToast = true;
                setTimeout(() => {
                    this.showCopyToast = false;
                }, 2000);
            } catch (err) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    this.showCopyToast = true;
                    setTimeout(() => {
                        this.showCopyToast = false;
                    }, 2000);
                } catch (err) {
                    console.error('コピーに失敗しました:', err);
                }
                document.body.removeChild(textArea);
            }
        }
    };
}
</script>
@endsection

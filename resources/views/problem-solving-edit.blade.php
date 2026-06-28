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
        <x-icon name="clipboard-document" class="w-5 h-5" />
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
        <a x-show="hasExistingRecord" href="/problem-solvings/list" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
            ← 一覧に戻る
        </a>
        <div x-show="!hasExistingRecord"></div>
        <div class="flex items-center gap-2">
            <!-- 編集トグルボタン（既存レコードのみ） -->
            <button
                x-show="hasExistingRecord"
                type="button"
                @click="isEditing ? saveAndStopEditing() : startEditing()"
                :disabled="isEditing && (submitting || floatingSaving)"
                class="inline-flex items-center justify-center p-2 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :class="isEditing ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50'"
                :title="isEditing ? '保存する' : '編集する'"
            >
                <span x-show="!isEditing"><x-icon name="pencil-square" class="w-5 h-5" /></span>
                <span x-show="isEditing"><x-icon name="check-circle" class="w-5 h-5" /></span>
            </button>
            <!-- 削除ボタン（既存レコードのみ） -->
            <button
                x-show="hasExistingRecord"
                @click="deleteItem()"
                class="text-red-400 hover:text-red-600 transition-colors p-2 rounded hover:bg-red-50"
                title="削除"
            >
                <x-icon name="trash" class="w-5 h-5" />
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
                        <x-icon name="tag" class="w-4 h-4" /> タグ
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

                <!-- Step 3: 実行計画（新規作成時のみ・複数可） -->
                <div x-show="isCreateMode && isEditing" class="border-t border-gray-200 pt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">3</span>
                        実行計画
                        <span class="text-gray-400 font-normal ml-1">いつ・どこで・どんなとき・誰と・何をどうする・妨げる要因と対策・検証方法</span>
                    </label>
                    <div class="space-y-3 mt-3">
                        <template x-for="(plan, index) in plans" :key="'new-plan-' + index">
                            <div class="border border-teal-200 rounded-lg p-3 bg-white">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-500 font-medium" x-text="'実行計画 ' + (index + 1)"></span>
                                    <button
                                        type="button"
                                        x-show="plans.length > 1"
                                        @click="removePlanRow(index)"
                                        class="text-red-400 hover:text-red-600 p-1"
                                        title="この実行計画を削除"
                                    >
                                        <x-icon name="trash" class="w-5 h-5" />
                                    </button>
                                </div>
                                <textarea
                                    x-model="plan.action_plan"
                                    rows="6"
                                    maxlength="5000"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    placeholder="例：明日の朝9時に、まず締め切りが近いものをリストアップする。…"
                                ></textarea>
                            </div>
                        </template>
                    </div>
                    <button
                        type="button"
                        @click="addPlanRow()"
                        class="mt-3 text-sm text-emerald-600 hover:text-emerald-800"
                    >
                        ＋ 実行計画を追加
                    </button>
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
                        <x-icon name="clipboard-document" class="w-5 h-5" /> 内容をコピー
                    </button>
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
        isCreateMode: itemId === null,
        isEditing: itemId === null,
        form: {
            problem_situation: '',
            improved_image: '',
            tag_ids: []
        },
        plans: [{ action_plan: '' }],
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

        async init() {
            await this.loadTags();

            if (this.hasExistingRecord) {
                await this.loadItem();
                if (this.isEditing) {
                    this.startAutoSave();
                }
            } else {
                this.startAutoSave();
            }

            this.takeSnapshot();
        },

        get selectedTagObjects() {
            return this.availableTags.filter(t => this.form.tag_ids.includes(t.id));
        },

        startEditing() {
            this.isEditing = true;
            this.takeSnapshot();
            this.startAutoSave();
        },

        async saveAndStopEditing() {
            if (!this.form.problem_situation.trim()) return;

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
                const res = await apiFetch('/api/tags');
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
                tag_ids: JSON.stringify(this.form.tag_ids),
                plans: JSON.stringify(this.plans.map(p => p.action_plan))
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
            return (
                this.form.problem_situation !== snapshot.problem_situation ||
                this.form.improved_image !== snapshot.improved_image ||
                JSON.stringify(this.form.tag_ids) !== snapshot.tag_ids ||
                JSON.stringify(this.plans.map(p => p.action_plan)) !== snapshot.plans
            );
        },

        addPlanRow() {
            this.plans.push({ action_plan: '' });
        },

        removePlanRow(index) {
            if (this.plans.length <= 1) return;
            this.plans.splice(index, 1);
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

        async performSave(isManual = false) {
            try {
                if (this.itemId) {
                    await this.saveExistingItem();
                    if (this.isCreateMode) {
                        await this.savePlans(this.itemId);
                    }
                } else {
                    await this.saveNewItem();
                }
                this.showSaveNotification(isManual);
            } catch (error) {
                console.error(isManual ? '保存に失敗しました:' : '自動保存に失敗しました:', error);
                if (isManual) throw error;
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
                const res = await apiFetch(`/api/problem-solvings/${this.itemId}`);
                if (res.ok) {
                    const item = await res.json();
                    this.form.problem_situation = item.problem_situation || '';
                    this.form.improved_image = item.improved_image || '';
                    this.form.tag_ids = item.tag_ids || [];
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async saveProblemSolving() {
            if (this.itemId) {
                await this.updateProblemSolving();
            } else {
                await this.createProblemSolving();
            }
        },

        async saveNewItem() {
            const res = await apiFetch('/api/problem-solvings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.form)
            });

            if (!res.ok) throw await parseApiErrorMessage(res);

            const created = await res.json();
            this.itemId = created.id;
            this.hasExistingRecord = true;

            if (this.isCreateMode) {
                await this.savePlans(created.id);
            }

            history.replaceState(null, '', `/problem-solvings/${created.id}`);
        },

        async savePlans(problemSolvingId) {
            for (let i = 0; i < this.plans.length; i++) {
                const plan = this.plans[i];
                if (!plan.action_plan || !plan.action_plan.trim()) continue;

                if (plan.id) {
                    await apiFetch(`/api/problem-solvings/${problemSolvingId}/plans/${plan.id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action_plan: plan.action_plan })
                    });
                } else {
                    const planRes = await apiFetch(`/api/problem-solvings/${problemSolvingId}/plans`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action_plan: plan.action_plan })
                    });
                    if (planRes.ok) {
                        const createdPlan = await planRes.json();
                        plan.id = createdPlan.id;
                    }
                }
            }
        },

        async saveExistingItem() {
            const res = await apiFetch(`/api/problem-solvings/${this.itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.form)
            });

            if (res.ok) {
                return;
            }
        },

        async createProblemSolving() {
            if (this.submitting || !this.form.problem_situation.trim()) return;

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
                const res = await apiFetch(`/api/problem-solvings/${this.itemId}`, {
                    method: 'DELETE'
                });

                if (res.ok) {
                    window.location.href = '/problem-solvings/list';
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
            if (this.plans.some(p => p.action_plan && p.action_plan.trim())) return true;
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

            const validPlans = this.plans.filter(p => p.action_plan && p.action_plan.trim());
            if (validPlans.length > 0) {
                sections.push('');
                validPlans.forEach((plan, index) => {
                    const label = validPlans.length > 1 ? `■ 実行計画 ${index + 1}` : '■ 実行計画';
                    sections.push(label);
                    sections.push(plan.action_plan.trim());
                });
            }

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

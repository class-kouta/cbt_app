@extends('layouts.app')

@section('title', isset($planId) ? '振り返り編集' : '振り返り')
@section('page-title', isset($planId) ? '振り返り編集' : '振り返り')

@section('content')
<div x-data="problemSolvingReflectionFormApp({{ $planId ?? 'null' }})" x-init="init()" x-cloak>

    <div x-show="showSaveToast" x-transition class="fixed top-16 right-4 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40">保存しました</div>

    <div class="flex justify-between items-center mb-4">
        <a href="/problem-solvings/plans" class="text-emerald-600 hover:text-emerald-800">← 振り返り一覧に戻る</a>
        <button x-show="hasExistingRecord" @click="deleteReflection()" class="text-red-400 hover:text-red-600 p-2" title="振り返りを削除">
            <x-icon name="trash" class="w-5 h-5" />
        </button>
    </div>

    <div x-show="loading" class="text-center py-16 bg-white rounded-xl shadow-md">
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <div x-show="!loading">
        <form @submit.prevent="save()" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">1</span>
                    問題状況 <span class="text-red-500">*</span>
                </label>
                <select x-model="form.problem_solving_id" @change="onProblemSolvingChange()" required :disabled="hasExistingRecord"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    :class="hasExistingRecord ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'">
                    <option value="">選択してください</option>
                    <template x-for="ps in problemSolvings" :key="ps.id">
                        <option :value="ps.id" x-text="ps.problem_situation"></option>
                    </template>
                </select>
                <p x-show="!hasExistingRecord && problemSolvings.length === 0" class="text-sm text-amber-600 mt-1">
                    問題解決シートがまだありません。<a href="/problem-solvings" class="underline">シートを作成</a>してください。
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">2</span>
                    実行計画 <span class="text-red-500">*</span>
                </label>
                <select x-model="form.plan_id" required :disabled="!form.problem_solving_id || hasExistingRecord"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    :class="form.problem_solving_id && !hasExistingRecord ? 'bg-white' : 'bg-gray-100 cursor-not-allowed'">
                    <option value="">選択してください</option>
                    <template x-for="plan in availablePlans" :key="plan.id">
                        <option :value="plan.id" x-text="planLabel(plan)"></option>
                    </template>
                </select>
                <p x-show="form.problem_solving_id && availablePlans.length === 0" class="text-sm text-amber-600 mt-1">
                    このシートには実行計画がまだありません。<a href="/problem-solvings" class="underline">シートを作成</a>して実行計画を登録してください。
                </p>
            </div>

            <div x-show="selectedPlanActionPlan" class="bg-teal-50 rounded-lg p-4 border border-teal-100">
                <span class="text-xs font-semibold text-teal-600 mb-2 block">選択中の実行計画</span>
                <p class="text-gray-800 text-sm whitespace-pre-wrap break-words" x-text="selectedPlanActionPlan"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">3</span>
                    振り返り <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal ml-1">実行後に記入：結果、うまくいったこと、改善点、学んだこと</span>
                </label>
                <textarea x-model="form.reflection" rows="8" maxlength="5000" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    placeholder="何が起きたか、予想は当たったか、学んだこと"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">4</span>
                    改善レベル（任意）
                    <span class="text-gray-400 font-normal ml-1">実行後の改善度合いを1〜10で評価</span>
                </label>
                <select x-model="form.improvement_level"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white">
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

            <button type="submit" :disabled="submitting || !canSubmit()"
                class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-3 rounded-lg font-medium disabled:opacity-50">
                <span x-text="submitting ? '保存中...' : (hasExistingRecord ? '更新する' : '保存する')"></span>
            </button>
        </form>
    </div>
</div>

<script>
function problemSolvingReflectionFormApp(planId) {
    return {
        planId,
        hasExistingRecord: planId !== null,
        loading: true,
        submitting: false,
        showSaveToast: false,
        problemSolvings: [],
        availablePlans: [],
        originalProblemSolvingId: '',
        form: {
            problem_solving_id: '',
            plan_id: '',
            reflection: '',
            improvement_level: ''
        },

        get selectedPlanActionPlan() {
            const plan = this.availablePlans.find(p => String(p.id) === String(this.form.plan_id));
            return plan?.action_plan || '';
        },

        async init() {
            await this.loadProblemSolvings();
            if (this.hasExistingRecord) {
                await this.loadPlan();
            } else {
                await this.applyQueryParams();
            }
            this.loading = false;
        },

        async applyQueryParams() {
            const params = new URLSearchParams(window.location.search);
            const psId = params.get('problem_solving_id');
            const prePlanId = params.get('plan_id');
            if (!psId) return;

            this.form.problem_solving_id = psId;
            await this.onProblemSolvingChange();
            if (prePlanId) {
                this.form.plan_id = prePlanId;
            }
        },

        planLabel(plan) {
            const text = (plan.action_plan || '').replace(/\s+/g, ' ').trim();
            if (!text) return '実行計画（未入力）';
            return text.length > 60 ? text.slice(0, 60) + '…' : text;
        },

        canSubmit() {
            return this.form.problem_solving_id && this.form.plan_id && this.form.reflection.trim() !== '';
        },

        async loadProblemSolvings() {
            this.problemSolvings = await fetchProblemSolvingOptions();
        },

        async onProblemSolvingChange() {
            this.form.plan_id = '';
            this.availablePlans = [];
            if (!this.form.problem_solving_id) return;

            const res = await apiFetch(`/api/problem-solvings/${this.form.problem_solving_id}`);
            if (!res.ok) return;

            const item = await res.json();
            this.availablePlans = (item.plans || []).filter(p => p.action_plan && p.action_plan.trim() !== '');
        },

        async loadPlan() {
            const res = await apiFetch(`/api/problem-solvings/plans/${this.planId}`);
            if (!res.ok) return;

            const plan = await res.json();
            this.originalProblemSolvingId = String(plan.problem_solving_id);
            this.form.problem_solving_id = this.originalProblemSolvingId;
            await this.onProblemSolvingChange();
            this.form.plan_id = String(plan.id);
            this.form.reflection = plan.reflection || '';
            this.form.improvement_level = plan.improvement_level ?? '';
        },

        async save() {
            if (!this.canSubmit() || this.submitting) return;
            this.submitting = true;

            const problemSolvingId = this.hasExistingRecord ? this.originalProblemSolvingId : this.form.problem_solving_id;
            const targetPlanId = this.hasExistingRecord ? this.planId : this.form.plan_id;

            const payload = {
                action_plan: this.selectedPlanActionPlan || null,
                reflection: this.form.reflection,
                improvement_level: this.form.improvement_level !== '' ? parseInt(this.form.improvement_level) : null
            };

            try {
                const res = await apiFetch(`/api/problem-solvings/${problemSolvingId}/plans/${targetPlanId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                if (!res.ok) throw await parseApiErrorMessage(res);

                if (!this.hasExistingRecord) {
                    window.location.href = `/problem-solvings/plans/${targetPlanId}`;
                    return;
                }

                this.showSaveToast = true;
                setTimeout(() => this.showSaveToast = false, 2000);
            } catch (e) {
                alert(typeof e === 'string' ? e : '保存に失敗しました');
            } finally {
                this.submitting = false;
            }
        },

        async deleteReflection() {
            if (!confirm('振り返りの内容を削除しますか？（実行計画は残ります）')) return;

            const res = await apiFetch(`/api/problem-solvings/${this.originalProblemSolvingId}/plans/${this.planId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action_plan: this.selectedPlanActionPlan || null,
                    reflection: null,
                    improvement_level: null
                })
            });

            if (res.ok) {
                this.form.reflection = '';
                this.form.improvement_level = '';
                window.location.href = '/problem-solvings/plans';
            } else {
                alert(await parseApiErrorMessage(res, '削除に失敗しました'));
            }
        }
    };
}
</script>
@endsection

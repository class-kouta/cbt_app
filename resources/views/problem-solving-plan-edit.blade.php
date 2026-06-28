@extends('layouts.app')

@section('title', isset($planId) ? '計画編集' : '計画作成')
@section('page-title', isset($planId) ? '計画編集' : '計画作成')

@section('content')
<div x-data="problemSolvingPlanFormApp({{ $planId ?? 'null' }})" x-init="init()" x-cloak>

    <div x-show="showSaveToast" x-transition class="fixed top-16 right-4 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40">保存しました</div>

    <div class="flex justify-between items-center mb-4">
        <a href="/problem-solvings/plans" class="text-emerald-600 hover:text-emerald-800">← 計画一覧に戻る</a>
        <button x-show="hasExistingRecord" @click="deleteItem()" class="text-red-400 hover:text-red-600 p-2" title="削除">
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
                <select x-model="form.problem_solving_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white">
                    <option value="">選択してください</option>
                    <template x-for="ps in problemSolvings" :key="ps.id">
                        <option :value="ps.id" x-text="ps.problem_situation"></option>
                    </template>
                </select>
                <p x-show="problemSolvings.length === 0" class="text-sm text-amber-600 mt-1">
                    問題解決シートがまだありません。<a href="/problem-solvings" class="underline">シートを作成</a>してください。
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">2</span>
                    実行計画 <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal ml-1">いつ・どこで・どんなとき・誰と・何をどうする・妨げる要因と対策・検証方法</span>
                </label>
                <textarea x-model="form.action_plan" rows="8" maxlength="5000" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    placeholder="例：明日の朝9時に、まず締め切りが近いものをリストアップする。…"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">3</span>
                    振り返り（任意）
                    <span class="text-gray-400 font-normal ml-1">実行後に記入：結果、うまくいったこと、改善点、学んだこと</span>
                </label>
                <textarea x-model="form.reflection" rows="6" maxlength="5000"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    placeholder="（実行後に記入してください）"></textarea>
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
function problemSolvingPlanFormApp(planId) {
    return {
        planId,
        hasExistingRecord: planId !== null,
        loading: true,
        submitting: false,
        showSaveToast: false,
        problemSolvings: [],
        form: {
            problem_solving_id: '',
            action_plan: '',
            reflection: '',
            improvement_level: ''
        },

        async init() {
            await this.loadProblemSolvings();
            if (this.hasExistingRecord) await this.loadPlan();
            this.loading = false;
        },

        canSubmit() {
            return this.form.problem_solving_id && this.form.action_plan.trim() !== '';
        },

        async loadProblemSolvings() {
            this.problemSolvings = await fetchProblemSolvingOptions();
        },

        async loadPlan() {
            const res = await apiFetch(`/api/problem-solvings/plans/${this.planId}`);
            if (!res.ok) return;
            const plan = await res.json();
            this.form.problem_solving_id = String(plan.problem_solving_id);
            this.form.action_plan = plan.action_plan || '';
            this.form.reflection = plan.reflection || '';
            this.form.improvement_level = plan.improvement_level ?? '';
        },

        async save() {
            if (!this.canSubmit() || this.submitting) return;
            this.submitting = true;
            const payload = {
                action_plan: this.form.action_plan || null,
                reflection: this.form.reflection || null,
                improvement_level: this.form.improvement_level !== '' ? parseInt(this.form.improvement_level) : null
            };
            try {
                let res;
                if (this.hasExistingRecord) {
                    res = await apiFetch(`/api/problem-solvings/${this.form.problem_solving_id}/plans/${this.planId}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                } else {
                    res = await apiFetch(`/api/problem-solvings/${this.form.problem_solving_id}/plans`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                }
                if (!res.ok) throw await parseApiErrorMessage(res);
                const saved = await res.json();
                if (!this.hasExistingRecord) {
                    window.location.href = `/problem-solvings/plans/${saved.id}`;
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

        async deleteItem() {
            if (!confirm('この計画を削除しますか？')) return;
            const res = await apiFetch(`/api/problem-solvings/${this.form.problem_solving_id}/plans/${this.planId}`, { method: 'DELETE' });
            if (res.ok) window.location.href = '/problem-solvings/plans';
            else alert(await parseApiErrorMessage(res, '削除に失敗しました'));
        }
    };
}
</script>
@endsection

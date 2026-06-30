@extends('layouts.app')

@section('title', isset($sessionId) ? '実施記録編集' : '実施記録作成')
@section('page-title', isset($sessionId) ? '実施記録編集' : '実施記録作成')

@section('content')
<div x-data="exposureSessionFormApp({{ $sessionId ?? 'null' }})" x-init="init()" x-cloak>

    <div x-show="showSaveToast" x-transition class="fixed top-16 right-4 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40">保存しました</div>

    <div class="flex justify-between items-center mb-4">
        <a href="/exposures/sessions" class="text-emerald-600 hover:text-emerald-800">← 実施記録一覧に戻る</a>
        <button x-show="hasExistingRecord" @click="deleteItem()" class="text-gray-500 hover:text-gray-700 p-2" title="削除">
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
                    回避していること <span class="text-red-500">*</span>
                </label>
                <select x-model="form.exposure_id" @change="onExposureChange()" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white">
                    <option value="">選択してください</option>
                    <template x-for="exposure in exposures" :key="exposure.id">
                        <option :value="exposure.id" x-text="exposure.avoidance_target"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">2</span>
                    不安階層表 <span class="text-red-500">*</span>
                </label>
                <select x-model="form.hierarchy_item_id" :disabled="!form.exposure_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    :class="form.exposure_id ? 'bg-white' : 'bg-gray-100 cursor-not-allowed'">
                    <option value="">選択してください</option>
                    <template x-for="item in hierarchyItems" :key="item.id">
                        <option :value="item.id" x-text="item.content"></option>
                    </template>
                </select>
                <p x-show="form.exposure_id && hierarchyItems.length === 0" class="text-sm text-amber-600 mt-1">
                    このシートには不安階層表がまだ登録されていません。<a href="/exposures" class="underline">シートを作成</a>してください。
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">3</span>
                    実施後の不安レベル <span class="text-red-500">*</span>
                </label>
                <select x-model="form.suds_after" required class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white">
                    <option value="">選択してください</option>
                    <template x-for="n in 21" :key="n">
                        <option :value="(n-1)*5" x-text="(n-1)*5"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">4</span>
                    振り返り（任意）
                </label>
                <textarea x-model="form.reflection" rows="6" maxlength="5000"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    placeholder="何が起きたか、予想は当たったか、学んだこと"></textarea>
            </div>

            <button type="submit" :disabled="submitting || !canSubmit()"
                class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-3 rounded-lg font-medium disabled:opacity-50">
                <span x-text="submitting ? '保存中...' : (hasExistingRecord ? '更新する' : '保存する')"></span>
            </button>
        </form>
    </div>
</div>

<script>
function exposureSessionFormApp(sessionId) {
    return {
        sessionId,
        hasExistingRecord: sessionId !== null,
        loading: true,
        submitting: false,
        showSaveToast: false,
        exposures: [],
        hierarchyItems: [],
        form: { exposure_id: '', hierarchy_item_id: '', suds_after: '', reflection: '' },

        async init() {
            await this.loadExposures();
            if (this.hasExistingRecord) await this.loadSession();
            this.loading = false;
        },

        canSubmit() {
            return this.form.exposure_id && this.form.hierarchy_item_id && this.form.suds_after !== '';
        },

        async loadExposures() {
            this.exposures = await fetchExposureOptions();
        },

        async onExposureChange() {
            this.form.hierarchy_item_id = '';
            this.hierarchyItems = [];
            if (!this.form.exposure_id) return;
            const res = await apiFetch(`/api/exposures/${this.form.exposure_id}`);
            if (!res.ok) return;
            const exposure = await res.json();
            this.hierarchyItems = exposure.hierarchy_items || [];
        },

        async loadSession() {
            const res = await apiFetch(`/api/exposures/sessions/${this.sessionId}`);
            if (!res.ok) return;
            const session = await res.json();
            this.form.exposure_id = String(session.exposure_id);
            await this.onExposureChange();
            this.form.hierarchy_item_id = session.hierarchy_item_id ? String(session.hierarchy_item_id) : '';
            this.form.suds_after = session.suds_after ?? '';
            this.form.reflection = session.reflection || '';
        },

        async save() {
            if (!this.canSubmit() || this.submitting) return;
            this.submitting = true;
            const payload = {
                hierarchy_item_id: parseInt(this.form.hierarchy_item_id),
                suds_after: parseInt(this.form.suds_after),
                reflection: this.form.reflection || null
            };
            try {
                let res;
                if (this.hasExistingRecord) {
                    res = await apiFetch(`/api/exposures/${this.form.exposure_id}/sessions/${this.sessionId}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                } else {
                    res = await apiFetch(`/api/exposures/${this.form.exposure_id}/sessions`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                }
                if (!res.ok) throw await parseApiErrorMessage(res);
                const saved = await res.json();
                if (!this.hasExistingRecord) {
                    window.location.href = `/exposures/sessions/${saved.id}`;
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
            if (!confirm('この実施記録を削除しますか？')) return;
            const res = await apiFetch(`/api/exposures/${this.form.exposure_id}/sessions/${this.sessionId}`, { method: 'DELETE' });
            if (res.ok) window.location.href = '/exposures/sessions';
            else alert(await parseApiErrorMessage(res, '削除に失敗しました'));
        }
    };
}
</script>
@endsection

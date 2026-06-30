@extends('layouts.app')

@section('title', 'エクスポージャー療法')
@section('page-title', 'エクスポージャー療法')

@section('content')
<div x-data="exposureFormApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>

    <div x-show="showManualSaveToast" x-transition class="fixed top-16 right-4 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40">保存しました</div>
    <div x-show="showSaveErrorToast" x-transition class="fixed top-16 right-4 bg-red-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40">保存に失敗しました</div>
    <div x-show="showCopyToast" x-transition class="fixed bottom-20 left-1/2 -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50">コピーしました！</div>

    <button x-show="isEditing" type="button" @click="manualSave()" :disabled="floatingSaving || !form.avoidance_target.trim()"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg z-30 disabled:opacity-50" title="保存する">
        <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zM16 20v-6H8v6M8 4v4h6"></path></svg>
    </button>

    <div class="flex justify-between items-center mb-4">
        <a x-show="hasExistingRecord" href="/exposures/list" class="text-emerald-600 hover:text-emerald-800">← 一覧に戻る</a>
        <div x-show="!hasExistingRecord"></div>
        <div class="flex items-center gap-2">
            <button x-show="hasExistingRecord" type="button" @click="isEditing ? saveAndStopEditing() : startEditing()"
                class="inline-flex items-center justify-center p-2 rounded-lg transition-colors"
                :class="isEditing ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50'"
                :title="isEditing ? '保存する' : '編集する'">
                <span x-show="!isEditing"><x-icon name="pencil-square" class="w-5 h-5" /></span>
                <span x-show="isEditing"><x-icon name="check-circle" class="w-5 h-5" /></span>
            </button>
            <button x-show="hasExistingRecord" @click="deleteItem()" class="text-gray-500 hover:text-gray-700 p-2" title="削除">
                <x-icon name="trash" class="w-5 h-5" />
            </button>
        </div>
    </div>

    <div x-show="loading && hasExistingRecord" class="text-center py-16 bg-white rounded-xl shadow-md">
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <div x-show="!loading || !hasExistingRecord">
        <form @submit.prevent="saveExposure()">
            <div class="space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">1</span>
                        回避していること・不安の対象 <span x-show="isEditing" class="text-red-500">*</span>
                    </label>
                    <textarea x-model="form.avoidance_target" rows="8" :disabled="!isEditing" maxlength="5000"
                        class="w-full border rounded-lg px-4 py-3" :class="isEditing ? 'border-gray-300 bg-white' : 'border-gray-200 bg-gray-50 cursor-not-allowed'"
                        placeholder="例：人前で話すことを避けている、電車に乗るのが怖い" :required="isEditing"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">2</span>
                        不安階層表
                        <span class="text-gray-400 font-normal ml-1">いちばん不安が少ないものから並べましょう</span>
                    </label>
                    <div class="space-y-3">
                        <template x-for="(item, index) in hierarchyItems" :key="item.id ? 'item-' + item.id : 'new-' + index">
                            <div class="border rounded-lg p-3" :class="isEditing ? 'border-gray-300' : 'border-gray-200 bg-gray-50'">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-500 font-medium" x-text="'場面 ' + (index + 1)"></span>
                                    <button x-show="isEditing" type="button" @click="removeHierarchyItem(index)" class="text-gray-500 hover:text-gray-700 p-1">
                                        <x-icon name="trash" class="w-5 h-5" />
                                    </button>
                                </div>
                                <textarea x-model="item.content" rows="2" :disabled="!isEditing" maxlength="500"
                                    class="w-full border rounded-lg px-3 py-2 mb-2" placeholder="例：1人でコンビニに行く"></textarea>
                                <label class="block text-xs text-gray-600 mb-1">不安レベル</label>
                                <select x-model="item.expected_suds" :disabled="!isEditing" class="w-full border rounded-lg px-3 py-2">
                                    <option value="">選択してください</option>
                                    <template x-for="n in 21" :key="n">
                                        <option :value="(n-1)*5" x-text="(n-1)*5"></option>
                                    </template>
                                </select>
                            </div>
                        </template>
                    </div>
                    <p x-show="!isEditing && hierarchyItems.filter(i => i.content.trim()).length === 0" class="text-gray-400 mt-2">未入力</p>
                    <button x-show="isEditing" type="button" @click="addHierarchyItemRow()" class="mt-2 text-sm text-emerald-600">＋ 場面を追加</button>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">3</span>
                        備考
                        <span class="text-gray-400 font-normal ml-1">その他メモがあれば書いてみましょう</span>
                    </label>
                    <textarea x-model="form.notes" rows="4" :disabled="!isEditing" maxlength="5000"
                        class="w-full border rounded-lg px-4 py-3" :class="isEditing ? 'border-gray-300 bg-white' : 'border-gray-200 bg-gray-50 cursor-not-allowed'"
                        placeholder="例：治療者からのアドバイス、気づいたことなど"></textarea>
                    <p x-show="!isEditing && !form.notes.trim()" class="text-gray-400 mt-1 text-sm">未入力</p>
                </div>

                <div class="space-y-3">
                    <button x-show="isEditing" type="submit" :disabled="submitting || !form.avoidance_target.trim()"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-3 rounded-lg font-medium disabled:opacity-50">
                        <span x-text="hasExistingRecord ? '更新する' : '保存する'"></span>
                    </button>
                    <button type="button" @click="copyToClipboard()" :disabled="!hasAnyContent()"
                        class="w-full border-2 border-gray-300 text-gray-700 py-3 rounded-xl disabled:opacity-50">内容をコピー</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function exposureFormApp(itemId) {
    return {
        itemId, hasExistingRecord: itemId !== null, isEditing: itemId === null,
        form: { avoidance_target: '', notes: '' },
        hierarchyItems: [{ content: '', expected_suds: '' }, { content: '', expected_suds: '' }, { content: '', expected_suds: '' }],
        loading: itemId !== null, submitting: false, floatingSaving: false,
        showManualSaveToast: false, showSaveErrorToast: false, showCopyToast: false,

        async init() {
            if (this.hasExistingRecord) {
                await this.loadItem();
            }
        },

        startEditing() { this.isEditing = true; },
        stopEditing() { this.isEditing = false; },

        async saveAndStopEditing() {
            if (!this.form.avoidance_target.trim()) return;
            this.submitting = true;
            try { await this.performSave(); this.stopEditing(); }
            catch (e) { this.showSaveErrorNotification(); }
            finally { this.submitting = false; }
        },

        async loadItem() {
            this.loading = true;
            try {
                const r = await apiFetch(`/api/exposures/${this.itemId}`);
                if (!r.ok) return;
                const item = await r.json();
                this.form.avoidance_target = item.avoidance_target || '';
                this.form.notes = item.notes || '';
                this.hierarchyItems = item.hierarchy_items.map(h => ({ id: h.id, content: h.content, expected_suds: h.expected_suds ?? '' }));
                while (this.hierarchyItems.length < 3) this.hierarchyItems.push({ content: '', expected_suds: '' });
            } finally { this.loading = false; }
        },

        addHierarchyItemRow() { this.hierarchyItems.push({ content: '', expected_suds: '' }); },
        removeHierarchyItem(i) { this.hierarchyItems.splice(i, 1); },

        async performSave() {
            if (this.itemId) await this.saveExistingItem();
            else await this.saveNewItem();
            this.showManualSaveToast = true;
            setTimeout(() => this.showManualSaveToast = false, 2000);
        },

        showSaveErrorNotification() {
            this.showSaveErrorToast = true;
            setTimeout(() => { this.showSaveErrorToast = false; }, 2000);
        },

        async manualSave() {
            if (this.floatingSaving || !this.form.avoidance_target.trim()) return;
            this.floatingSaving = true;
            try {
                await this.performSave();
            } catch (e) {
                this.showSaveErrorNotification();
            } finally {
                this.floatingSaving = false;
            }
        },

        async saveNewItem() {
            const r = await apiFetch('/api/exposures', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(this.form) });
            if (!r.ok) throw await parseApiErrorMessage(r);
            const created = await r.json();
            this.itemId = created.id;
            this.hasExistingRecord = true;
            await this.saveHierarchyItems(created.id);
            history.replaceState(null, '', `/exposures/${created.id}`);
        },

        async saveExistingItem() {
            const r = await apiFetch(`/api/exposures/${this.itemId}`, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(this.form) });
            if (!r.ok) throw await parseApiErrorMessage(r);
            await this.saveHierarchyItems(this.itemId);
        },

        async saveHierarchyItems(exposureId) {
            const valid = this.hierarchyItems.filter(i => i.content.trim());
            const res = await apiFetch(`/api/exposures/${exposureId}/hierarchy-items/sync`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    items: valid.map((item, i) => ({
                        id: item.id || null,
                        content: item.content,
                        sort_order: i + 1,
                        expected_suds: item.expected_suds !== '' ? parseInt(item.expected_suds) : null
                    }))
                })
            });
            if (!res.ok) throw await parseApiErrorMessage(res);
            const result = await res.json();
            const saved = result.items || [];
            this.hierarchyItems = saved.map(s => ({ id: s.id, content: s.content, expected_suds: s.expected_suds ?? '' }));
            while (this.hierarchyItems.length < 3) this.hierarchyItems.push({ content: '', expected_suds: '' });
        },

        async saveExposure() {
            if (this.submitting || !this.form.avoidance_target.trim()) return;
            this.submitting = true;
            try { await this.performSave(); this.stopEditing(); }
            catch (e) { this.showSaveErrorNotification(); }
            finally { this.submitting = false; }
        },

        async deleteItem() {
            if (!confirm('この記録を削除しますか？')) return;
            const r = await apiFetch(`/api/exposures/${this.itemId}`, { method: 'DELETE' });
            if (r.ok) window.location.href = '/exposures/list';
            else alert(await parseApiErrorMessage(r, '削除に失敗しました'));
        },

        hasAnyContent() {
            return this.form.avoidance_target.trim() || this.form.notes.trim() || this.hierarchyItems.some(i => i.content.trim());
        },

        generateCopyText() {
            const lines = ['【エクスポージャー療法】', '', '■ 回避していること', this.form.avoidance_target.trim() || '未入力'];
            const items = this.hierarchyItems.filter(i => i.content.trim());
            lines.push('', '■ 不安階層表');
            lines.push(items.length ? items.map((i, idx) => `${idx+1}. ${i.content}${i.expected_suds !== '' ? ' (不安レベル:'+i.expected_suds+')' : ''}`).join('\n') : '未入力');
            lines.push('', '■ 備考', this.form.notes.trim() || '未入力');
            return lines.join('\n');
        },

        async copyToClipboard() {
            try { await navigator.clipboard.writeText(this.generateCopyText()); }
            catch (e) { /* fallback omitted */ }
            this.showCopyToast = true;
            setTimeout(() => this.showCopyToast = false, 2000);
        }
    };
}
</script>
@endsection

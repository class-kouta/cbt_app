@extends('layouts.app')

@section('title', 'エクスポージャー療法')
@section('page-title', 'エクスポージャー療法')

@section('content')
<div x-data="exposureFormApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>

    <div x-show="showAutoSaveToast" x-transition class="fixed top-16 right-4 bg-orange-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40">自動保存しました</div>
    <div x-show="showManualSaveToast" x-transition class="fixed top-16 right-4 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40">保存しました</div>
    <div x-show="showCopyToast" x-transition class="fixed bottom-20 left-1/2 -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50">コピーしました！</div>

    <button x-show="isEditing" type="button" @click="manualSave()" :disabled="floatingSaving || !form.avoidance_target.trim()"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg z-30 disabled:opacity-50" title="保存する">
        <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zM16 20v-6H8v6M8 4v4h6"></path></svg>
    </button>

    <div class="flex justify-between items-center mb-4">
        <a x-show="hasExistingRecord" :href="backUrl" class="text-emerald-600 hover:text-emerald-800">← <span x-text="backLabel"></span></a>
        <div x-show="!hasExistingRecord"></div>
        <div class="flex items-center gap-2">
            <button x-show="hasExistingRecord" type="button" @click="isEditing ? saveAndStopEditing() : startEditing()"
                class="inline-flex items-center justify-center p-2 rounded-lg transition-colors"
                :class="isEditing ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50'"
                :title="isEditing ? '保存する' : '編集する'">
                <span x-show="!isEditing"><x-icon name="pencil-square" class="w-5 h-5" /></span>
                <span x-show="isEditing"><x-icon name="check-circle" class="w-5 h-5" /></span>
            </button>
            <button x-show="hasExistingRecord" @click="deleteItem()" class="text-red-400 hover:text-red-600 p-2" title="削除">
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

                <!-- Step 1 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">1</span>
                        回避していること・不安の対象 <span x-show="isEditing" class="text-red-500">*</span>
                    </label>
                    <textarea x-model="form.avoidance_target" rows="8" :disabled="!isEditing" maxlength="5000"
                        class="w-full border rounded-lg px-4 py-3" :class="isEditing ? 'border-gray-300 bg-white' : 'border-gray-200 bg-gray-50 cursor-not-allowed'"
                        placeholder="例：人前で話すことを避けている、電車に乗るのが怖い" :required="isEditing"></textarea>
                </div>

                <!-- Step 2 -->
                <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white text-xs font-bold mr-1">2</span>
                        心構え（チェックリスト・参考用）
                    </label>
                    <ul class="text-sm text-gray-600 space-y-1 mb-3 list-disc list-inside">
                        <li>不安は一時的に高まっても、時間とともに下がることが多い</li>
                        <li>不安を感じながらも、そのままいることは安全なことが多い</li>
                        <li>完璧に不安ゼロになるまで待たなくていい</li>
                        <li>小さな一歩から始めよう</li>
                    </ul>
                    <label class="block text-sm font-medium text-gray-700 mb-1">自分への声かけ（任意）</label>
                    <textarea x-model="form.self_talk" rows="4" :disabled="!isEditing" maxlength="2000"
                        class="w-full border rounded-lg px-4 py-3" :class="isEditing ? 'bg-white' : 'bg-gray-50 cursor-not-allowed'"
                        placeholder="例：不安を感じても大丈夫。少しずつ慣れていこう。"></textarea>
                </div>

                <!-- Step 3 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">3</span>
                        不安階層表
                        <span class="text-gray-400 font-normal ml-1">いちばん不安が少ないものから並べましょう</span>
                    </label>
                    <div class="space-y-3">
                        <template x-for="(item, index) in hierarchyItems" :key="index">
                            <div class="border rounded-lg p-3" :class="isEditing ? 'border-gray-300' : 'border-gray-200 bg-gray-50'">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-500 font-medium" x-text="'場面 ' + (index + 1)"></span>
                                    <button x-show="isEditing" type="button" @click="removeHierarchyItem(index)" class="text-red-400 p-1">
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

                <!-- Step 4: Sessions -->
                <div class="border-t border-gray-200 pt-5">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">実施記録</h3>
                    <div class="space-y-4">
                        <template x-for="(session, index) in sessions" :key="session.id || 'new-' + index">
                            <div :id="session.id ? 'session-' + session.id : null"
                                class="border border-teal-200 rounded-xl overflow-hidden bg-white shadow-sm"
                                :class="scrollTargetSessionId && session.id && String(session.id) === String(scrollTargetSessionId) ? 'ring-2 ring-emerald-400' : ''">
                                <div class="bg-gradient-to-r from-teal-50 to-lime-50 px-4 py-3 flex justify-between cursor-pointer" @click="session.expanded = !session.expanded">
                                    <span class="text-sm font-medium text-gray-700" x-text="'実施記録 ' + (index + 1)"></span>
                                    <div class="flex items-center gap-2">
                                        <button x-show="isEditing && (sessions.length > 1 || !session.id)" type="button" @click.stop="deleteSession(index)" class="text-red-400 p-1">
                                            <x-icon name="trash" class="w-5 h-5" />
                                        </button>
                                        <svg class="w-5 h-5 text-gray-400" :class="session.expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                                <div x-show="session.expanded" x-collapse class="px-4 py-4 space-y-4">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">不安階層表から選択（任意）</label>
                                        <select x-model="session.hierarchy_item_id" :disabled="!isEditing" class="w-full border rounded-lg px-3 py-2">
                                            <option value="">選択してください</option>
                                            <template x-for="item in savedHierarchyItems" :key="item.id">
                                                <option :value="item.id" x-text="item.content"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">実施計画</label>
                                        <textarea x-model="session.action_plan" rows="5" :disabled="!isEditing" maxlength="5000"
                                            class="w-full border rounded-lg px-4 py-3" placeholder="いつ・どこで・何をするか"></textarea>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">実施前 不安レベル</label>
                                            <select x-model="session.suds_before" :disabled="!isEditing" class="w-full border rounded-lg px-2 py-2 text-sm">
                                                <option value="">-</option>
                                                <template x-for="n in 21" :key="'b'+n"><option :value="(n-1)*5" x-text="(n-1)*5"></option></template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">最高 不安レベル</label>
                                            <select x-model="session.suds_peak" :disabled="!isEditing" class="w-full border rounded-lg px-2 py-2 text-sm">
                                                <option value="">-</option>
                                                <template x-for="n in 21" :key="'p'+n"><option :value="(n-1)*5" x-text="(n-1)*5"></option></template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">実施後 不安レベル</label>
                                            <select x-model="session.suds_after" :disabled="!isEditing" class="w-full border rounded-lg px-2 py-2 text-sm">
                                                <option value="">-</option>
                                                <template x-for="n in 21" :key="'a'+n"><option :value="(n-1)*5" x-text="(n-1)*5"></option></template>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">振り返り</label>
                                        <textarea x-model="session.reflection" rows="5" :disabled="!isEditing" maxlength="5000"
                                            class="w-full border rounded-lg px-4 py-3" placeholder="何が起きたか、予想は当たったか、学んだこと"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button x-show="isEditing" type="button" @click="addNewSession()"
                        class="mt-4 w-full py-3 border-2 border-dashed border-teal-300 text-teal-600 rounded-xl">＋ 実施記録を追加</button>
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
        form: { avoidance_target: '', self_talk: '' },
        hierarchyItems: [{ content: '', expected_suds: '' }, { content: '', expected_suds: '' }, { content: '', expected_suds: '' }],
        originalHierarchyItems: [],
        sessions: [],
        originalSessions: [],
        savedHierarchyItems: [],
        loading: itemId !== null, submitting: false, floatingSaving: false,
        showManualSaveToast: false, showAutoSaveToast: false, showCopyToast: false,
        autoSaveSnapshots: [], autoSaveInterval: null, autoSaving: false,
        fromPage: 'list', scrollTargetSessionId: null,

        get backUrl() { return this.fromPage === 'sessions' ? '/exposures/sessions' : '/exposures/list'; },
        get backLabel() { return this.fromPage === 'sessions' ? '実施記録一覧に戻る' : '一覧に戻る'; },
        async init() {
            const p = new URLSearchParams(window.location.search);
            this.fromPage = p.get('from') || 'list';
            this.scrollTargetSessionId = p.get('session_id') || null;
            if (this.hasExistingRecord && this.fromPage === 'sessions') this.isEditing = true;
            if (this.hasExistingRecord) {
                await this.loadItem();
                if (this.isEditing) this.startAutoSave();
                this.scrollToSessionIfNeeded();
            } else {
                this.sessions = [this.emptySession(1)];
                this.startAutoSave();
            }
            this.takeSnapshot();
        },

        emptySession(num) {
            return { id: null, session_number: num, hierarchy_item_id: '', action_plan: '', suds_before: '', suds_peak: '', suds_after: '', reflection: '', expanded: true };
        },

        scrollToSessionIfNeeded() {
            if (!this.scrollTargetSessionId) return;
            this.$nextTick(() => setTimeout(() => {
                document.getElementById('session-' + this.scrollTargetSessionId)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100));
        },

        startEditing() { this.isEditing = true; this.takeSnapshot(); this.startAutoSave(); },
        stopEditing() { this.isEditing = false; this.stopAutoSave(); },

        async saveAndStopEditing() {
            if (!this.form.avoidance_target.trim()) return;
            this.submitting = true;
            try { await this.performSave(true); this.stopEditing(); }
            catch (e) { alert('保存に失敗しました'); }
            finally { this.submitting = false; }
        },

        startAutoSave() {
            this.stopAutoSave();
            this.autoSaveInterval = setInterval(() => this.checkAndAutoSave(), 30000);
        },
        stopAutoSave() {
            if (this.autoSaveInterval) clearInterval(this.autoSaveInterval);
            this.autoSaveInterval = null;
            this.autoSaveSnapshots = [];
        },

        takeSnapshot() {
            const s = {
                form: JSON.stringify(this.form),
                hierarchyItems: JSON.stringify(this.hierarchyItems),
                sessions: JSON.stringify(this.sessions.map(s => ({ action_plan: s.action_plan, reflection: s.reflection, suds_before: s.suds_before, suds_peak: s.suds_peak, suds_after: s.suds_after })))
            };
            this.autoSaveSnapshots.push(s);
            if (this.autoSaveSnapshots.length > 2) this.autoSaveSnapshots.shift();
        },

        hasChanged() {
            if (this.autoSaveSnapshots.length < 1) return false;
            const old = this.autoSaveSnapshots[0];
            const curSessions = JSON.stringify(this.sessions.map(s => ({ action_plan: s.action_plan, reflection: s.reflection, suds_before: s.suds_before, suds_peak: s.suds_peak, suds_after: s.suds_after })));
            return JSON.stringify(this.form) !== old.form || JSON.stringify(this.hierarchyItems) !== old.hierarchyItems || curSessions !== old.sessions;
        },

        async checkAndAutoSave() {
            if (!this.isEditing || !this.form.avoidance_target.trim() || !this.hasChanged() || this.submitting || this.autoSaving) {
                this.takeSnapshot(); return;
            }
            this.autoSaving = true;
            try { await this.performSave(false); this.showAutoSaveToast = true; setTimeout(() => this.showAutoSaveToast = false, 2000); }
            finally { this.autoSaving = false; this.takeSnapshot(); }
        },

        async loadItem() {
            this.loading = true;
            try {
                const r = await apiFetch(`/api/exposures/${this.itemId}`);
                if (!r.ok) return;
                const item = await r.json();
                this.form.avoidance_target = item.avoidance_target || '';
                this.form.self_talk = item.self_talk || '';

                this.hierarchyItems = item.hierarchy_items.map(h => ({ id: h.id, content: h.content, expected_suds: h.expected_suds ?? '' }));
                this.originalHierarchyItems = JSON.parse(JSON.stringify(this.hierarchyItems));
                this.savedHierarchyItems = item.hierarchy_items;

                if (item.sessions?.length) {
                    this.sessions = item.sessions.map(s => ({
                        id: s.id, session_number: s.session_number,
                        hierarchy_item_id: s.hierarchy_item_id ? String(s.hierarchy_item_id) : '',
                        action_plan: s.action_plan || '', suds_before: s.suds_before ?? '', suds_peak: s.suds_peak ?? '', suds_after: s.suds_after ?? '',
                        reflection: s.reflection || '', expanded: true
                    }));
                } else {
                    this.sessions = [this.emptySession(1)];
                }
                this.originalSessions = JSON.parse(JSON.stringify(this.sessions));
                while (this.hierarchyItems.length < 3) this.hierarchyItems.push({ content: '', expected_suds: '' });
            } finally { this.loading = false; }
        },

        addHierarchyItemRow() { this.hierarchyItems.push({ content: '', expected_suds: '' }); },
        removeHierarchyItem(i) { this.hierarchyItems.splice(i, 1); },
        addNewSession() {
            const n = this.sessions.length ? Math.max(...this.sessions.map(s => s.session_number)) + 1 : 1;
            this.sessions.push(this.emptySession(n));
        },
        deleteSession(i) { this.sessions.splice(i, 1); },

        async performSave(isManual) {
            if (this.itemId) await this.saveExistingItem();
            else await this.saveNewItem();
            if (isManual) { this.showManualSaveToast = true; setTimeout(() => this.showManualSaveToast = false, 2000); }
        },

        async manualSave() {
            if (this.floatingSaving || !this.form.avoidance_target.trim()) return;
            this.floatingSaving = true;
            try { await this.performSave(true); } finally { this.floatingSaving = false; }
        },

        async saveNewItem() {
            const r = await apiFetch('/api/exposures', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(this.form) });
            if (!r.ok) throw new Error('create failed');
            const created = await r.json();
            this.itemId = created.id;
            this.hasExistingRecord = true;
            await this.saveHierarchyItems(created.id);
            await this.saveSessions(created.id);
            history.replaceState(null, '', `/exposures/${created.id}`);
        },

        async saveExistingItem() {
            const r = await apiFetch(`/api/exposures/${this.itemId}`, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(this.form) });
            if (!r.ok) throw new Error('update failed');
            await this.saveHierarchyItems(this.itemId);
            await this.saveSessions(this.itemId);
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
            if (!res.ok) throw new Error('hierarchy sync failed');
            const result = await res.json();
            const saved = result.items || [];
            this.originalHierarchyItems = saved.map(s => ({ id: s.id, content: s.content, expected_suds: s.expected_suds ?? '' }));
            this.savedHierarchyItems = this.originalHierarchyItems;
            this.hierarchyItems = [...this.originalHierarchyItems];
            while (this.hierarchyItems.length < 3) this.hierarchyItems.push({ content: '', expected_suds: '' });
        },

        async saveSessions(exposureId) {
            const payload = this.sessions
                .filter(session => session.id || (session.action_plan && session.action_plan.trim()) || (session.reflection && session.reflection.trim()))
                .map(session => ({
                    id: session.id || null,
                    hierarchy_item_id: session.hierarchy_item_id ? parseInt(session.hierarchy_item_id) : null,
                    action_plan: session.action_plan || null,
                    suds_before: session.suds_before !== '' ? parseInt(session.suds_before) : null,
                    suds_peak: session.suds_peak !== '' ? parseInt(session.suds_peak) : null,
                    suds_after: session.suds_after !== '' ? parseInt(session.suds_after) : null,
                    reflection: session.reflection || null
                }));

            const res = await apiFetch(`/api/exposures/${exposureId}/sessions/sync`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sessions: payload })
            });
            if (!res.ok) throw new Error('sessions sync failed');
            const result = await res.json();
            const saved = result.sessions || [];
            this.sessions = saved.length
                ? saved.map(s => ({
                    id: s.id,
                    session_number: s.session_number,
                    hierarchy_item_id: s.hierarchy_item_id ? String(s.hierarchy_item_id) : '',
                    action_plan: s.action_plan || '',
                    suds_before: s.suds_before ?? '',
                    suds_peak: s.suds_peak ?? '',
                    suds_after: s.suds_after ?? '',
                    reflection: s.reflection || '',
                    expanded: true
                }))
                : [this.emptySession(1)];
            this.originalSessions = JSON.parse(JSON.stringify(this.sessions));
        },

        async saveExposure() {
            if (this.submitting || !this.form.avoidance_target.trim()) return;
            this.submitting = true;
            try { await this.performSave(true); this.stopEditing(); }
            catch (e) { alert('保存に失敗しました'); }
            finally { this.submitting = false; }
        },

        async deleteItem() {
            if (!confirm('この記録を削除しますか？')) return;
            const r = await apiFetch(`/api/exposures/${this.itemId}`, { method: 'DELETE' });
            if (r.ok) window.location.href = this.backUrl;
            else alert('削除に失敗しました');
        },

        hasAnyContent() {
            return this.form.avoidance_target.trim() || this.form.self_talk.trim() || this.hierarchyItems.some(i => i.content.trim()) || this.sessions.some(s => s.action_plan?.trim() || s.reflection?.trim());
        },

        generateCopyText() {
            const lines = ['【エクスポージャー療法】', '', '■ 回避していること', this.form.avoidance_target.trim() || '未入力'];
            if (this.form.self_talk.trim()) { lines.push('', '■ 自分への声かけ', this.form.self_talk.trim()); }
            const items = this.hierarchyItems.filter(i => i.content.trim());
            lines.push('', '■ 不安階層表');
            lines.push(items.length ? items.map((i, idx) => `${idx+1}. ${i.content}${i.expected_suds !== '' ? ' (不安レベル:'+i.expected_suds+')' : ''}`).join('\n') : '未入力');
            this.sessions.forEach((s, idx) => {
                lines.push('', `■ 実施記録 ${idx+1}`, s.action_plan?.trim() || '未入力');
                if (s.suds_before !== '' || s.suds_after !== '') lines.push(`不安レベル: ${s.suds_before||'-'} → ${s.suds_peak||'-'} → ${s.suds_after||'-'}`);
                lines.push('振り返り: ' + (s.reflection?.trim() || '未入力'));
            });
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

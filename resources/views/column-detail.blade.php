@extends('layouts.app')

@section('title', 'コラム詳細')

@section('content')
<div x-data="columnDetailApp()" x-init="init()" x-cloak>
    <!-- ローディング -->
    <div x-show="loading" class="text-center py-16">
        <svg class="animate-spin h-8 w-8 mx-auto text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- コンテンツ -->
    <div x-show="!loading && column" class="space-y-4">
        <!-- ヘッダー -->
        <div class="flex items-center justify-between mb-4">
            <a href="/columns/list" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors">
                ←
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500" x-text="formatDate(column?.created_at)"></span>
                <!-- 編集モード切り替えボタン -->
                <button
                    x-show="!isEditMode"
                    @click="isEditMode = true"
                    class="text-indigo-600 hover:text-indigo-800 transition-colors p-2 rounded hover:bg-indigo-50"
                    title="編集する"
                >
                    ✏️
                </button>
                <button
                    @click="deleteColumn()"
                    class="text-red-400 hover:text-red-600 transition-colors p-2 rounded hover:bg-red-50"
                    title="削除"
                >
                    🗑️
                </button>
            </div>
        </div>

        <!-- 閲覧モード -->
        <div x-show="!isEditMode" class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2"></div>
            <div class="p-5 space-y-4">
                <!-- 状況 -->
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-indigo-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-500 text-white text-xs">1</span>
                        状況
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" x-text="column?.situation || '未入力'"></p>
                </div>

                <!-- 気分 -->
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-indigo-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-500 text-white text-xs">2</span>
                        気分
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.mood ? 'text-gray-400' : ''" x-text="column?.mood || '未入力'"></p>
                </div>

                <!-- 自動思考 -->
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-indigo-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-500 text-white text-xs">3</span>
                        自動思考
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.automatic_thought ? 'text-gray-400' : ''" x-text="column?.automatic_thought || '未入力'"></p>
                </div>

                <!-- 根拠と反証 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 根拠 -->
                    <div class="bg-amber-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-amber-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 text-white text-xs">4</span>
                            根拠
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.evidence ? 'text-gray-400' : ''" x-text="column?.evidence || '未入力'"></p>
                    </div>

                    <!-- 反証 -->
                    <div class="bg-teal-50 rounded-lg p-4">
                        <div class="text-xs font-semibold text-teal-600 mb-2 flex items-center gap-1">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-teal-500 text-white text-xs">5</span>
                            反証
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.counter_evidence ? 'text-gray-400' : ''" x-text="column?.counter_evidence || '未入力'"></p>
                    </div>
                </div>

                <!-- 適応的思考 -->
                <div class="bg-emerald-50 rounded-lg p-4 border-2 border-emerald-200">
                    <div class="text-xs font-semibold text-emerald-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white text-xs">6</span>
                        適応的思考 ✨
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere font-medium" :class="!column?.adaptive_thought ? 'text-gray-400' : ''" x-text="column?.adaptive_thought || '未入力'"></p>
                </div>

                <!-- いまの気分 -->
                <div class="bg-pink-50 rounded-lg p-4">
                    <div class="text-xs font-semibold text-pink-600 mb-2 flex items-center gap-1">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-pink-500 text-white text-xs">7</span>
                        いまの気分
                    </div>
                    <p class="text-gray-800 whitespace-pre-wrap break-words overflow-wrap-anywhere" :class="!column?.current_mood ? 'text-gray-400' : ''" x-text="column?.current_mood || '未入力'"></p>
                </div>
            </div>
        </div>

        <!-- 編集モード -->
        <div x-show="isEditMode" class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl shadow-lg p-6 border border-indigo-100">
            <form @submit.prevent="updateColumn()">
                <div class="space-y-5">
                    <!-- (1) 状況 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-500 text-white text-xs font-bold mr-1">1</span>
                            状況 <span class="text-red-500">*</span>
                            <span class="text-gray-400 font-normal ml-1">気持ちが動揺したときの一場面</span>
                        </label>
                        <textarea
                            x-model="editData.situation"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="例：会議で自分の意見を否定された"
                            maxlength="1000"
                            required
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="editData.situation.length + '/1000'"></div>
                    </div>

                    <!-- (2) 気分 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-500 text-white text-xs font-bold mr-1">2</span>
                            気分
                            <span class="text-gray-400 font-normal ml-1">そのときの気持ち</span>
                        </label>
                        <textarea
                            x-model="editData.mood"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="例：悲しい(80%) 恥ずかしい(60%)"
                            maxlength="500"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="editData.mood.length + '/500'"></div>
                    </div>

                    <!-- (3) 自動思考 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-500 text-white text-xs font-bold mr-1">3</span>
                            自動思考
                            <span class="text-gray-400 font-normal ml-1">そのとき頭に浮かんだこと</span>
                        </label>
                        <textarea
                            x-model="editData.automatic_thought"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="例：自分は仕事ができない人間だ"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="editData.automatic_thought.length + '/1000'"></div>
                    </div>

                    <!-- (4) 根拠 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white text-xs font-bold mr-1">4</span>
                            根拠
                            <span class="text-gray-400 font-normal ml-1">自動思考を裏付ける具体的な事実</span>
                        </label>
                        <textarea
                            x-model="editData.evidence"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                            placeholder="例：提案が採用されなかった"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="editData.evidence.length + '/1000'"></div>
                    </div>

                    <!-- (5) 反証 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-500 text-white text-xs font-bold mr-1">5</span>
                            反証
                            <span class="text-gray-400 font-normal ml-1">自動思考と反対の事実</span>
                        </label>
                        <textarea
                            x-model="editData.counter_evidence"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                            placeholder="例：先月の提案は採用されて好評だった"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="editData.counter_evidence.length + '/1000'"></div>
                    </div>

                    <!-- (6) 適応的思考 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">6</span>
                            適応的思考
                            <span class="text-gray-400 font-normal ml-1">バランスのよい考え</span>
                        </label>
                        <textarea
                            x-model="editData.adaptive_thought"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                            placeholder="例：今回は合わなかっただけで、自分には良い提案もできる"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="editData.adaptive_thought.length + '/1000'"></div>
                    </div>

                    <!-- (7) いまの気分 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-pink-500 text-white text-xs font-bold mr-1">7</span>
                            いまの気分
                            <span class="text-gray-400 font-normal ml-1">コラムを書き終えた後の気持ち</span>
                        </label>
                        <textarea
                            x-model="editData.current_mood"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                            placeholder="例：悲しい(40%) 少し楽になった"
                            maxlength="500"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="editData.current_mood.length + '/500'"></div>
                    </div>

                    <!-- エラーメッセージ -->
                    <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>

                    <!-- ボタン -->
                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="cancelEdit()"
                            class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-300 transition-all"
                            :disabled="saving"
                        >
                            キャンセル
                        </button>
                        <button
                            type="submit"
                            class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-3 px-6 rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-600 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="saving || !editData.situation.trim()"
                        >
                            <span x-show="!saving" class="flex items-center justify-center gap-2">
                                💾 保存
                            </span>
                            <span x-show="saving" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                保存中...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- エラー -->
    <div x-show="!loading && !column" class="text-center py-16 bg-white rounded-xl shadow-md">
        <p class="text-6xl mb-4">😢</p>
        <p class="text-gray-600 text-lg mb-2">コラムが見つかりません</p>
        <a href="/columns/list" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800">
            ←
        </a>
    </div>
</div>

<script>
function columnDetailApp() {
    return {
        column: null,
        loading: true,
        columnId: {{ $columnId }},
        isEditMode: false,
        saving: false,
        error: '',
        editData: {
            situation: '',
            mood: '',
            automatic_thought: '',
            evidence: '',
            counter_evidence: '',
            adaptive_thought: '',
            current_mood: ''
        },

        async init() {
            await this.loadColumn();
        },

        async loadColumn() {
            try {
                const res = await fetch(`/api/columns/${this.columnId}`);
                if (res.ok) {
                    this.column = await res.json();
                    // 編集データを初期化
                    this.resetEditData();
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        resetEditData() {
            this.editData = {
                situation: this.column?.situation || '',
                mood: this.column?.mood || '',
                automatic_thought: this.column?.automatic_thought || '',
                evidence: this.column?.evidence || '',
                counter_evidence: this.column?.counter_evidence || '',
                adaptive_thought: this.column?.adaptive_thought || '',
                current_mood: this.column?.current_mood || ''
            };
        },

        cancelEdit() {
            this.isEditMode = false;
            this.error = '';
            this.resetEditData();
        },

        async updateColumn() {
            this.error = '';
            
            if (!this.editData.situation.trim()) {
                this.error = '状況を入力してください';
                return;
            }

            this.saving = true;
            try {
                const res = await fetch(`/api/columns/${this.columnId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.editData)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                // 更新成功
                this.column = await res.json();
                this.isEditMode = false;
                this.resetEditData();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.saving = false;
            }
        },

        async deleteColumn() {
            if (!confirm('このコラムを削除しますか？')) return;

            try {
                await fetch(`/api/columns/${this.columnId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                window.location.href = '/columns/list';
            } catch (e) {
                console.error(e);
            }
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    };
}
</script>
@endsection

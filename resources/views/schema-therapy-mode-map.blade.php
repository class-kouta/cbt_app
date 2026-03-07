@extends('layouts.app')

@section('title', 'モードマップ(簡易版) - ココロの避難所')
@section('page-title', 'モードマップ(簡易版)')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div x-data="modeMapApp()" x-init="init()" x-cloak class="max-w-4xl mx-auto">
    <!-- 手動保存トースト -->
    <div
        x-show="showSaveToast"
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

    <!-- エラートースト -->
    <div
        x-show="showErrorToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-16 right-4 bg-red-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span x-text="errorMessage"></span>
    </div>

    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-6 sm:p-8 md:p-10 border border-gray-200">
        <!-- ヘッダー -->
        <div class="flex items-center justify-between mb-6">
            <a href="/schema-therapy" class="inline-flex items-center gap-1 text-green-600 hover:text-green-700 font-medium transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                戻る
            </a>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="exportCsv()"
                    :disabled="exporting"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-green-400 transition-all text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <template x-if="!exporting">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </template>
                    <template x-if="exporting">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                    </template>
                    <span x-text="exporting ? 'エクスポート中...' : 'CSV出力'"></span>
                </button>
                <button
                    type="button"
                    @click="isEditing ? saveAndStopEditing() : startEditing()"
                    :disabled="isEditing && saving"
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
                <template x-if="isEditing && !saving">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="isEditing && saving">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <span x-text="isEditing ? (saving ? '保存中...' : '保存して編集をやめる') : '編集する'"></span>
                </button>
            </div>
        </div>

        <!-- ローディング -->
        <div x-show="loading" class="text-center py-16">
            <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 mt-2">読み込み中...</p>
        </div>

        <!-- フォーム -->
        <div x-show="!loading" class="space-y-6">
            <!-- 傷ついた子どもモード -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-500 text-white text-xs font-bold">1</span>
                        傷ついた子どもモード
                    </span>
                </label>
                <textarea
                    x-model="formData.wounded_child_mode"
                    rows="20"
                    :disabled="!isEditing"
                    class="w-full border rounded-lg px-4 py-3 transition-all resize-y"
                    :class="isEditing
                        ? 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white'
                        : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                    placeholder="傷ついた子どもモードについて自由に書いてください..."
                    maxlength="10000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="(formData.wounded_child_mode || '').length + '/10000'"></div>
            </div>

            <!-- 傷つける大人モード -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-500 text-white text-xs font-bold">2</span>
                        傷つける大人モード
                    </span>
                </label>
                <textarea
                    x-model="formData.hurtful_adult_mode"
                    rows="20"
                    :disabled="!isEditing"
                    class="w-full border rounded-lg px-4 py-3 transition-all resize-y"
                    :class="isEditing
                        ? 'border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white'
                        : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                    placeholder="傷つける大人モードについて自由に書いてください..."
                    maxlength="10000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="(formData.hurtful_adult_mode || '').length + '/10000'"></div>
            </div>

            <!-- いただけない対処モード -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white text-xs font-bold">3</span>
                        いただけない対処モード
                    </span>
                </label>
                <textarea
                    x-model="formData.unacceptable_coping_mode"
                    rows="20"
                    :disabled="!isEditing"
                    class="w-full border rounded-lg px-4 py-3 transition-all resize-y"
                    :class="isEditing
                        ? 'border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent bg-white'
                        : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                    placeholder="いただけない対処モードについて自由に書いてください..."
                    maxlength="10000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="(formData.unacceptable_coping_mode || '').length + '/10000'"></div>
            </div>

            <!-- ヘルシーモード(幸せな子どもモード) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold">4</span>
                        ヘルシーモード(幸せな子どもモード)
                    </span>
                </label>
                <textarea
                    x-model="formData.healthy_happy_child_mode"
                    rows="20"
                    :disabled="!isEditing"
                    class="w-full border rounded-lg px-4 py-3 transition-all resize-y"
                    :class="isEditing
                        ? 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white'
                        : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                    placeholder="ヘルシーモード(幸せな子どもモード)について自由に書いてください..."
                    maxlength="10000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="(formData.healthy_happy_child_mode || '').length + '/10000'"></div>
            </div>

            <!-- ヘルシーモード(ヘルシーな大人モード) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold">5</span>
                        ヘルシーモード(ヘルシーな大人モード)
                    </span>
                </label>
                <textarea
                    x-model="formData.healthy_adult_mode"
                    rows="20"
                    :disabled="!isEditing"
                    class="w-full border rounded-lg px-4 py-3 transition-all resize-y"
                    :class="isEditing
                        ? 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white'
                        : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                    placeholder="ヘルシーモード(ヘルシーな大人モード)について自由に書いてください..."
                    maxlength="10000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="(formData.healthy_adult_mode || '').length + '/10000'"></div>
            </div>
        </div>
    </div>

    <!-- フローティング保存ボタン -->
    <button
        x-show="isEditing"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        type="button"
        @click="save()"
        :disabled="saving"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center hover:from-emerald-600 hover:to-teal-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed z-30"
        title="保存する"
    >
        <template x-if="!saving">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zM16 20v-6H8v6M8 4v4h6"></path>
            </svg>
        </template>
        <template x-if="saving">
            <svg class="animate-spin w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </template>
    </button>
</div>

<script>
function modeMapApp() {
    return {
        recordId: null,
        formData: {
            wounded_child_mode: '',
            hurtful_adult_mode: '',
            unacceptable_coping_mode: '',
            healthy_happy_child_mode: '',
            healthy_adult_mode: ''
        },
        originalData: {
            wounded_child_mode: '',
            hurtful_adult_mode: '',
            unacceptable_coping_mode: '',
            healthy_happy_child_mode: '',
            healthy_adult_mode: ''
        },
        loading: true,
        saving: false,
        autoSaving: false,
        isEditing: false,
        showSaveToast: false,
        showAutoSaveToast: false,
        showErrorToast: false,
        errorMessage: '',
        exporting: false,

        autoSaveInterval: null,
        autoSaveSnapshots: [],

        async init() {
            await this.loadData();
        },

        async loadData() {
            this.loading = true;
            try {
                const res = await fetch('/api/mode-maps');
                if (res.ok) {
                    const data = await res.json();
                    this.recordId = data.id;
                    this.formData.wounded_child_mode = data.wounded_child_mode || '';
                    this.formData.hurtful_adult_mode = data.hurtful_adult_mode || '';
                    this.formData.unacceptable_coping_mode = data.unacceptable_coping_mode || '';
                    this.formData.healthy_happy_child_mode = data.healthy_happy_child_mode || '';
                    this.formData.healthy_adult_mode = data.healthy_adult_mode || '';
                    this.originalData = { ...this.formData };
                }
            } catch (error) {
                console.error('データの取得に失敗しました:', error);
            } finally {
                this.loading = false;
            }
        },

        startEditing() {
            this.isEditing = true;
            this.takeSnapshot();
            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        async saveAndStopEditing() {
            await this.performSave(true);
            this.stopEditing();
        },

        stopEditing() {
            this.isEditing = false;
            if (this.autoSaveInterval) {
                clearInterval(this.autoSaveInterval);
                this.autoSaveInterval = null;
            }
            this.autoSaveSnapshots = [];
        },

        takeSnapshot() {
            const snapshot = { ...this.formData };
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
            return this.hasValueChanged(this.autoSaveSnapshots[0]);
        },

        hasValueChanged(snapshot) {
            return (
                this.formData.wounded_child_mode !== snapshot.wounded_child_mode ||
                this.formData.hurtful_adult_mode !== snapshot.hurtful_adult_mode ||
                this.formData.unacceptable_coping_mode !== snapshot.unacceptable_coping_mode ||
                this.formData.healthy_happy_child_mode !== snapshot.healthy_happy_child_mode ||
                this.formData.healthy_adult_mode !== snapshot.healthy_adult_mode
            );
        },

        async checkAndAutoSave() {
            if (!this.isEditing) return;

            if (
                this.hasChangedFromPreviousSnapshot() &&
                !this.saving &&
                !this.autoSaving
            ) {
                await this.performAutoSave();
            }
            this.takeSnapshot();
        },

        async performAutoSave() {
            this.autoSaving = true;
            try {
                await this.performSave(false);
            } finally {
                this.autoSaving = false;
            }
        },

        async performSave(isManual) {
            if (this.saving) return;

            this.saving = true;
            try {
                let res;
                if (this.recordId) {
                    res = await fetch(`/api/mode-maps/${this.recordId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });
                } else {
                    res = await fetch('/api/mode-maps', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });
                }

                if (res.ok) {
                    const data = await res.json();
                    this.recordId = data.id;
                    this.originalData = { ...this.formData };

                    if (isManual) {
                        this.showSaveToast = true;
                        setTimeout(() => { this.showSaveToast = false; }, 2000);
                    } else {
                        this.showAutoSaveToast = true;
                        setTimeout(() => { this.showAutoSaveToast = false; }, 2000);
                    }
                } else {
                    const errorData = await res.json();
                    this.errorMessage = errorData.message || '保存に失敗しました';
                    this.showErrorToast = true;
                    setTimeout(() => { this.showErrorToast = false; }, 3000);
                }
            } catch (error) {
                console.error('保存に失敗しました:', error);
                this.errorMessage = '保存に失敗しました';
                this.showErrorToast = true;
                setTimeout(() => { this.showErrorToast = false; }, 3000);
            } finally {
                this.saving = false;
            }
        },

        async save() {
            await this.performSave(true);
        },

        async exportCsv() {
            this.exporting = true;
            try {
                await exportCsvFromApi(
                    '/api/mode-maps/export/csv',
                    {},
                    'mode_maps.csv',
                    'モードマップ'
                );
            } catch (error) {
                console.error('CSVエクスポートに失敗しました:', error);
                this.errorMessage = 'CSVエクスポートに失敗しました';
                this.showErrorToast = true;
                setTimeout(() => { this.showErrorToast = false; }, 3000);
            } finally {
                this.exporting = false;
            }
        }
    };
}
</script>
@endsection

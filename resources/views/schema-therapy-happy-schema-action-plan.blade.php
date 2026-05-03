@extends('layouts.app')

@section('title', 'ハッピースキーマと行動計画 - ココケア')
@section('page-title', 'ハッピースキーマと行動計画')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div x-data="happySchemaActionPlanApp()" x-init="init()" x-cloak class="max-w-4xl mx-auto">
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
            <!-- ハッピースキーマ -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-500 text-white text-xs font-bold">1</span>
                        ハッピースキーマ
                    </span>
                </label>
                <textarea
                    x-model="formData.happy_schema"
                    rows="20"
                    :disabled="!isEditing"
                    class="w-full border rounded-lg px-4 py-3 transition-all resize-y"
                    :class="isEditing
                        ? 'border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white'
                        : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                    placeholder="ハッピースキーマについて自由に書いてください..."
                    maxlength="10000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="(formData.happy_schema || '').length + '/10000'"></div>
            </div>

            <!-- ハッピースキーマに基づく行動計画 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold">2</span>
                        ハッピースキーマに基づく行動計画
                    </span>
                </label>
                <textarea
                    x-model="formData.action_plan"
                    rows="20"
                    :disabled="!isEditing"
                    class="w-full border rounded-lg px-4 py-3 transition-all resize-y"
                    :class="isEditing
                        ? 'border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white'
                        : 'border-gray-200 bg-gray-50 text-gray-700 cursor-not-allowed'"
                    placeholder="ハッピースキーマに基づく行動計画について自由に書いてください..."
                    maxlength="10000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="(formData.action_plan || '').length + '/10000'"></div>
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
function happySchemaActionPlanApp() {
    return {
        recordId: null,
        formData: {
            happy_schema: '',
            action_plan: ''
        },
        originalData: {
            happy_schema: '',
            action_plan: ''
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

        async init() {
            await this.loadData();
        },

        async loadData() {
            this.loading = true;
            try {
                const res = await fetch('/api/happy-schema-action-plans');
                if (res.ok) {
                    const data = await res.json();
                    this.recordId = data.id;
                    this.formData.happy_schema = data.happy_schema || '';
                    this.formData.action_plan = data.action_plan || '';
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
        },

        async checkAndAutoSave() {
            if (!this.isEditing) return;

            const hasChanged = this.formData.happy_schema !== this.originalData.happy_schema ||
                               this.formData.action_plan !== this.originalData.action_plan;

            if (hasChanged && !this.saving && !this.autoSaving) {
                await this.performAutoSave();
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

        async performSave(isManual) {
            if (this.saving) return;

            this.saving = true;
            try {
                const url = this.recordId
                    ? `/api/happy-schema-action-plans/${this.recordId}`
                    : '/api/happy-schema-action-plans';
                const method = this.recordId ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

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
                    '/api/happy-schema-action-plans/export/csv',
                    {},
                    'happy_schema_action_plans.csv',
                    'ハッピースキーマと行動計画'
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

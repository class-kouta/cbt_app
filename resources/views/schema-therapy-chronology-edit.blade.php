@extends('layouts.app')

@section('title', '年表作成 - ココロの避難所')
@section('page-title', '年表')

@section('content')
<div x-data="chronologyEditApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <!-- 編集モード時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a href="/schema-therapy/chronology" class="text-green-600 hover:text-green-800 flex items-center gap-1">
            ← 一覧に戻る
        </a>
    </div>

    <!-- ローディング（編集モードのみ） -->
    <div x-show="loading && isEditMode" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-green-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
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

    <!-- 手動保存トースト -->
    <div
        x-show="showManualSaveToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-16 right-4 bg-green-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        保存しました
    </div>

    <!-- フローティング保存ボタン -->
    <button
        type="button"
        @click="manualSave()"
        :disabled="floatingSaving || !isFormValid()"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center hover:from-green-600 hover:to-emerald-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed z-30"
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

    <!-- フォーム -->
    <div x-show="!loading || !isEditMode">
    <form @submit.prevent="saveChronology()">
        <div class="space-y-5">
            <!-- いつ -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500 text-white text-xs font-bold mr-1">1</span>
                    いつ <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal ml-1">時期や年齢など</span>
                </label>
                <input
                    type="text"
                    x-model="formData.when_period"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                    placeholder="例：5歳の頃、小学校3年生、2020年春"
                    maxlength="200"
                    required
                >
                <div class="text-xs text-gray-400 text-right" x-text="formData.when_period.length + '/200'"></div>
            </div>

            <!-- 環境・出来事 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500 text-white text-xs font-bold mr-1">2</span>
                    環境・出来事
                    <span class="text-gray-400 font-normal ml-1">そのときの状況や起きたこと</span>
                </label>
                <textarea
                    x-model="formData.environment_event"
                    rows="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                    placeholder="例：両親が離婚した。転校することになった。"
                    maxlength="10000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="formData.environment_event.length + '/10000'"></div>
            </div>

            <!-- 体験・感じたこと・思ったこと -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500 text-white text-xs font-bold mr-1">3</span>
                    体験・感じたこと・思ったこと
                    <span class="text-gray-400 font-normal ml-1">そのとき自分が体験したこと</span>
                </label>
                <textarea
                    x-model="formData.experience_feeling"
                    rows="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                    placeholder="例：とても不安だった。自分のせいだと思った。"
                    maxlength="10000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="formData.experience_feeling.length + '/10000'"></div>
            </div>

            <!-- エラーメッセージ -->
            <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>

            <!-- 保存ボタン -->
            <div class="space-y-3">
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-green-500 to-emerald-500 text-white py-4 px-6 rounded-xl font-semibold hover:from-green-600 hover:to-emerald-600 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="submitting || !isFormValid()"
                >
                    <span x-show="!submitting && !isEditMode" class="flex items-center justify-center gap-2">
                        ✨ 年表を保存
                    </span>
                    <span x-show="!submitting && isEditMode" class="flex items-center justify-center gap-2">
                        ✨ 更新する
                    </span>
                    <span x-show="submitting" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isEditMode ? '更新中...' : '保存中...'"></span>
                    </span>
                </button>
            </div>
        </div>
    </form>
    </div>
</div>

<script>
function chronologyEditApp(itemId) {
    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        formData: {
            when_period: '',
            environment_event: '',
            experience_feeling: ''
        },
        loading: false,
        submitting: false,
        error: '',
        showAutoSaveToast: false,
        showManualSaveToast: false,
        floatingSaving: false,

        autoSaveSnapshots: [],
        autoSaveInterval: null,
        autoSaving: false,

        async init() {
            if (this.isEditMode) {
                await this.loadChronology();
            }

            this.takeSnapshot();

            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        async loadChronology() {
            this.loading = true;
            try {
                const res = await fetch(`/api/chronologies/${this.itemId}`);
                if (res.ok) {
                    const data = await res.json();
                    this.formData.when_period = data.when_period || '';
                    this.formData.environment_event = data.environment_event || '';
                    this.formData.experience_feeling = data.experience_feeling || '';
                }
            } catch (error) {
                // エラー詳細はセキュリティ上コンソールに出力しない
            } finally {
                this.loading = false;
            }
        },

        isFormValid() {
            return this.formData.when_period.trim();
        },

        takeSnapshot() {
            const snapshot = {
                when_period: this.formData.when_period,
                environment_event: this.formData.environment_event,
                experience_feeling: this.formData.experience_feeling
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
                this.formData.when_period !== snapshot.when_period ||
                this.formData.environment_event !== snapshot.environment_event ||
                this.formData.experience_feeling !== snapshot.experience_feeling
            );
        },

        async checkAndAutoSave() {
            if (
                this.formData.when_period.trim() &&
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
                    const res = await fetch(`/api/chronologies/${this.itemId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        this.showSaveNotification(isManual);
                    }
                } else {
                    const res = await fetch('/api/chronologies', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        const data = await res.json();
                        this.itemId = data.id;
                        this.isEditMode = true;
                        history.replaceState(null, '', `/schema-therapy/chronology/${this.itemId}/edit`);
                        this.showSaveNotification(isManual);
                    }
                }
            } catch (error) {
                // エラー詳細はセキュリティ上コンソールに出力しない
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
            if (this.floatingSaving || !this.isFormValid()) return;

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

        async saveChronology() {
            if (this.isEditMode) {
                await this.updateChronology();
            } else {
                await this.createChronology();
            }
        },

        async createChronology() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = '「いつ」を入力してください';
                return;
            }

            this.submitting = true;
            try {
                const res = await fetch('/api/chronologies', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                window.location.href = '/schema-therapy/chronology';
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        },

        async updateChronology() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = '「いつ」を入力してください';
                return;
            }

            this.submitting = true;
            try {
                const res = await fetch(`/api/chronologies/${this.itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                window.location.href = '/schema-therapy/chronology';
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection

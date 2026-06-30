@extends('layouts.app')

@section('title', 'スキーマ年表 - ' . config('app.name'))
@section('page-title', 'スキーマ年表')

@section('content')
<div x-data="chronologyEditApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <!-- 編集モード時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a href="/schema-therapy/chronology" class="text-green-600 hover:text-green-800 flex items-center gap-1">
            ← 一覧に戻る
        </a>
        <button
            @click="confirmDelete()"
            class="text-red-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50 flex items-center gap-1 text-sm"
            title="削除"
        >
            <x-icon name="trash" class="w-5 h-5" /><span class="hidden sm:inline">削除</span>
        </button>
    </div>

    <!-- ローディング（編集モードのみ） -->
    <div x-show="loading && isEditMode" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-green-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
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

            <!-- タグ（ポジティブ / ネガティブ） -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500 text-white text-xs font-bold mr-1">4</span>
                    タグ
                    <span class="text-gray-400 font-normal ml-1">この出来事の印象</span>
                </label>
                <div class="flex gap-3">
                    <button
                        type="button"
                        @click="formData.sentiment_type = formData.sentiment_type === 'positive' ? '' : 'positive'"
                        :class="formData.sentiment_type === 'positive'
                            ? 'bg-orange-100 border-orange-400 text-orange-700 ring-2 ring-orange-300'
                            : 'bg-white border-gray-300 text-gray-600 hover:border-orange-300 hover:bg-orange-50'"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 font-medium transition-all"
                    >
                        <x-icon name="face-smile" class="w-4 h-4 inline-block" /> ポジティブ
                    </button>
                    <button
                        type="button"
                        @click="formData.sentiment_type = formData.sentiment_type === 'negative' ? '' : 'negative'"
                        :class="formData.sentiment_type === 'negative'
                            ? 'bg-blue-100 border-blue-400 text-blue-700 ring-2 ring-blue-300'
                            : 'bg-white border-gray-300 text-gray-600 hover:border-blue-300 hover:bg-blue-50'"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 font-medium transition-all"
                    >
                        <x-icon name="face-frown" class="w-4 h-4 inline-block" /> ネガティブ
                    </button>
                </div>
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
                        <x-icon name="arrow-down-tray" class="w-5 h-5" /> 年表を保存
                    </span>
                    <span x-show="!submitting && isEditMode" class="flex items-center justify-center gap-2">
                        <x-icon name="arrow-down-tray" class="w-5 h-5" /> 更新する
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

    <!-- 削除確認モーダル -->
    <div
        x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="showDeleteModal = false"
    >
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showDeleteModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl"
                @click.stop
            >
                <div class="bg-gradient-to-r from-red-500 to-rose-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <x-icon name="trash" class="w-5 h-5" /> 削除確認
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <p class="text-gray-700 text-base">この年表を削除しますか？</p>
                    <p class="text-sm text-gray-500 mt-2">この操作は取り消せません。</p>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="flex-1 py-2.5 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                    >
                        キャンセル
                    </button>
                    <button
                        type="button"
                        @click="executeDelete()"
                        :disabled="deleting"
                        class="flex-1 py-2.5 px-4 bg-gradient-to-r from-red-500 to-rose-500 text-white rounded-lg font-medium hover:from-red-600 hover:to-rose-600 transition-colors disabled:opacity-50"
                    >
                        <span x-show="!deleting">削除する</span>
                        <span x-show="deleting" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            削除中...
                        </span>
                    </button>
                </div>
            </div>
        </div>
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
            experience_feeling: '',
            sentiment_type: ''
        },
        loading: false,
        submitting: false,
        error: '',
        showManualSaveToast: false,
        floatingSaving: false,
        showDeleteModal: false,
        deleting: false,

        async init() {
            if (this.isEditMode) {
                await this.loadChronology();
            }
        },

        async loadChronology() {
            this.loading = true;
            try {
                const res = await apiFetch(`/api/chronologies/${this.itemId}`);
                if (res.ok) {
                    const data = await res.json();
                    this.formData.when_period = data.when_period || '';
                    this.formData.environment_event = data.environment_event || '';
                    this.formData.experience_feeling = data.experience_feeling || '';
                    this.formData.sentiment_type = data.sentiment_type || '';
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

        async performSave() {
            try {
                if (this.itemId) {
                    const res = await apiFetch(`/api/chronologies/${this.itemId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        this.showSaveNotification();
                    }
                } else {
                    const res = await apiFetch('/api/chronologies', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        const data = await res.json();
                        this.itemId = data.id;
                        this.isEditMode = true;
                        history.replaceState(null, '', `/schema-therapy/chronology/${this.itemId}/edit`);
                        this.showSaveNotification();
                    }
                }
            } catch (error) {
                // エラー詳細はセキュリティ上コンソールに出力しない
            }
        },

        async manualSave() {
            if (this.floatingSaving || !this.isFormValid()) return;

            this.floatingSaving = true;
            try {
                await this.performSave();
            } finally {
                this.floatingSaving = false;
            }
        },

        showSaveNotification() {
            this.showManualSaveToast = true;
            setTimeout(() => {
                this.showManualSaveToast = false;
            }, 2000);
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
                const res = await apiFetch('/api/chronologies', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
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

        confirmDelete() {
            this.showDeleteModal = true;
        },

        async executeDelete() {
            if (!this.itemId || this.deleting) return;

            this.deleting = true;
            this.error = '';
            try {
                const res = await apiFetch(`/api/chronologies/${this.itemId}`, {
                    method: 'DELETE',
                });

                if (res.ok || res.status === 204) {
                    window.location.href = '/schema-therapy/chronology';
                } else {
                    this.error = '削除中にエラーが発生しました。';
                    this.showDeleteModal = false;
                }
            } catch (error) {
                this.error = '削除中にエラーが発生しました。';
                this.showDeleteModal = false;
            } finally {
                this.deleting = false;
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
                const res = await apiFetch(`/api/chronologies/${this.itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
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

@extends('layouts.app')

@section('title', '不安日記')
@section('page-title', '不安日記')

@section('content')
<div x-data="anxietyDiaryApp({{ $itemId ?? 'null' }})" x-init="init()" @destroy.window="destroy()" x-cloak>
    <!-- 編集モード時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a :href="'/anxiety-diaries/' + itemId" class="text-amber-600 hover:text-amber-800 flex items-center gap-1">
            ← 詳細に戻る
        </a>
    </div>

    <!-- ストレッサーとストレス反応から転記ボタン（新規作成モードかつ未転記かつデータがある場合のみ表示） -->
    <div x-show="stressorAndResponses.length > 0 && !isEditMode && !hasTransferred" class="mb-4">
        <button
            type="button"
            @click="showStressorModal = true"
            class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-3 px-4 rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-600 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            ストレッサーとストレス反応から転記する
        </button>
    </div>

    <!-- ストレッサーとストレス反応選択モーダル -->
    <div
        x-show="showStressorModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="showStressorModal = false"
    >
        <!-- オーバーレイ -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showStressorModal = false"></div>

        <!-- モーダルコンテンツ -->
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl"
                @click.stop
            >
                <!-- ヘッダー -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            転記元を選択
                        </h3>
                        <button
                            type="button"
                            @click="showStressorModal = false"
                            class="text-white hover:text-indigo-100 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-indigo-100 mt-1">
                        選択すると「状況」「どんな不安が思い浮かんだか」に転記されます
                    </p>
                </div>

                <!-- コンテンツ -->
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="item in stressorAndResponses" :key="item.id">
                        <div
                            @click="showTransferConfirm(item)"
                            class="px-6 py-4 border-b border-gray-100 cursor-pointer group"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <!-- ストレッサー（状況に転記） -->
                                    <div class="mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 mb-1">
                                            状況へ
                                        </span>
                                        <p class="text-sm text-gray-800 line-clamp-2" x-text="item.stressor"></p>
                                    </div>
                                    <!-- 認知（自動思考を不安に転記） -->
                                    <div x-show="item.cognition">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 mb-1">
                                            不安へ
                                        </span>
                                        <p class="text-sm text-gray-600 line-clamp-1" x-text="item.cognition"></p>
                                    </div>
                                    <!-- 作成日時 -->
                                    <p class="text-xs text-gray-400 mt-2" x-text="formatDate(item.created_at)"></p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- フッター -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button
                        type="button"
                        @click="showStressorModal = false"
                        class="w-full py-2.5 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                    >
                        キャンセル
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 転記確認ダイアログ -->
    <div
        x-show="showTransferConfirmModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] overflow-y-auto"
        @keydown.escape.window="showTransferConfirmModal = false"
    >
        <!-- オーバーレイ -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showTransferConfirmModal = false"></div>

        <!-- モーダルコンテンツ -->
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
                <!-- ヘッダー -->
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        確認
                    </h3>
                </div>

                <!-- コンテンツ -->
                <div class="px-6 py-5">
                    <p class="text-gray-700 text-base mb-4">
                        転記してよろしいですか？
                    </p>
                    <p class="text-sm text-gray-500">
                        「状況」「どんな不安が思い浮かんだか」に内容が転記されます。
                    </p>
                </div>

                <!-- フッター -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-3">
                    <button
                        type="button"
                        @click="showTransferConfirmModal = false"
                        class="flex-1 py-2.5 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                    >
                        キャンセル
                    </button>
                    <button
                        type="button"
                        @click="confirmTransfer()"
                        class="flex-1 py-2.5 px-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-lg font-medium hover:from-indigo-600 hover:to-purple-600 transition-colors"
                    >
                        転記する
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 転記成功トースト -->
    <div
        x-show="showTransferToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2"
    >
        <span>📝</span>
        <span>転記しました！</span>
    </div>

    <!-- ローディング（編集モードのみ） -->
    <div x-show="loading && isEditMode" class="text-center py-16 bg-white rounded-xl shadow-md">
        <svg class="animate-spin h-8 w-8 text-amber-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
        class="fixed top-16 right-4 bg-amber-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
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
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center hover:from-amber-600 hover:to-orange-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed z-30"
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

    <!-- 不安日記作成/編集フォーム -->
    <div x-show="!loading || !isEditMode">
    <form @submit.prevent="saveItem()">
        <div class="space-y-5">
            <!-- (1) 状況 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white text-xs font-bold mr-1">1</span>
                    状況 <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal ml-1">不安を感じた場面</span>
                </label>
                <textarea
                    x-model="formData.situation"
                    rows="6"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                    placeholder="例：明日のプレゼンのことを考えた"
                    maxlength="1000"
                    required
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="formData.situation.length + '/1000'"></div>
            </div>

            <!-- (2) どんな不安が思い浮かんだか -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold mr-1">2</span>
                    どんな不安が思い浮かんだか
                    <span class="text-gray-400 font-normal ml-1">頭に浮かんだ不安な考え</span>
                </label>
                <textarea
                    x-model="formData.anxiety_thought"
                    rows="6"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                    placeholder="例：うまく話せなくて恥をかくかもしれない"
                    maxlength="1000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="formData.anxiety_thought.length + '/1000'"></div>
            </div>

            <!-- (3) 実際にどうなったか -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500 text-white text-xs font-bold mr-1">3</span>
                    実際にどうなったか
                    <span class="text-gray-400 font-normal ml-1">結果として何が起きたか</span>
                </label>
                <textarea
                    x-model="formData.actual_outcome"
                    rows="6"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                    placeholder="例：練習通りに話せた。質問にも答えられた。"
                    maxlength="1000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="formData.actual_outcome.length + '/1000'"></div>
            </div>

            <!-- エラーメッセージ -->
            <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>

            <!-- ボタンエリア -->
            <div class="space-y-3">
                <!-- 送信ボタン -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-4 px-6 rounded-xl font-semibold hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="submitting || !isFormValid()"
                >
                    <span x-show="!submitting && !isEditMode" class="flex items-center justify-center gap-2">
                        ✨ 保存する
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
function anxietyDiaryApp(itemId) {
    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        formData: {
            situation: '',
            anxiety_thought: '',
            actual_outcome: '',
            stressor_and_response_id: null
        },
        loading: false,
        submitting: false,
        error: '',
        showAutoSaveToast: false,
        showManualSaveToast: false,
        floatingSaving: false,

        // 保存処理の排他制御用
        isSaving: false,

        // 自動保存用
        autoSaveSnapshots: [],
        autoSaveInterval: null,
        autoSaving: false,

        // ストレッサーとストレス反応からの転記機能
        stressorAndResponses: [],
        showStressorModal: false,
        showTransferToast: false,
        hasTransferred: false,
        selectedStressorItem: null,
        showTransferConfirmModal: false,

        async init() {
            // ストレッサーとストレス反応一覧を取得
            await this.loadStressorAndResponses();

            if (this.isEditMode) {
                await this.loadItem();
            }

            // 初期スナップショットを取得
            this.takeSnapshot();

            // 30秒ごとに自動保存チェック
            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        // コンポーネント破棄時のクリーンアップ
        destroy() {
            if (this.autoSaveInterval) {
                clearInterval(this.autoSaveInterval);
                this.autoSaveInterval = null;
            }
        },

        // ストレッサーとストレス反応一覧を取得
        async loadStressorAndResponses() {
            try {
                const res = await fetch('/api/stressor-and-responses');
                if (res.ok) {
                    this.stressorAndResponses = await res.json();
                }
            } catch (error) {
                console.error('ストレッサーとストレス反応の取得に失敗しました:', error);
            }
        },

        // 転記確認ダイアログを表示
        showTransferConfirm(item) {
            this.selectedStressorItem = item;
            this.showTransferConfirmModal = true;
        },

        // 転記を確認して実行
        async confirmTransfer() {
            if (!this.selectedStressorItem) return;

            // 状況 ← ストレッサー
            this.formData.situation = this.selectedStressorItem.stressor || '';
            // どんな不安が思い浮かんだか ← 認知（自動思考）
            this.formData.anxiety_thought = this.selectedStressorItem.cognition || '';
            // 転記元のストレッサーとストレス反応ID
            this.formData.stressor_and_response_id = this.selectedStressorItem.id;

            // 確認ダイアログを閉じる
            this.showTransferConfirmModal = false;
            // 選択モーダルも閉じる
            this.showStressorModal = false;
            // 転記済みフラグをセット
            this.hasTransferred = true;

            // 転記成功トーストを表示
            this.showTransferToast = true;
            setTimeout(() => {
                this.showTransferToast = false;
            }, 2000);

            // 転記後に自動保存を実行
            await this.performAutoSave();

            // 選択されたアイテムをクリア
            this.selectedStressorItem = null;
        },

        // 日時をフォーマット
        formatDate(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}/${month}/${day} ${hours}:${minutes}`;
        },

        // 現在の値のスナップショットを取得
        takeSnapshot() {
            const snapshot = {
                situation: this.formData.situation,
                anxiety_thought: this.formData.anxiety_thought,
                actual_outcome: this.formData.actual_outcome
            };
            this.autoSaveSnapshots.push(snapshot);

            // 直近2つ分のみ保持
            if (this.autoSaveSnapshots.length > 2) {
                this.autoSaveSnapshots.shift();
            }
        },

        // 60秒前のスナップショットと現在の値を比較
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

        // 指定されたスナップショットと現在の値を比較
        hasValueChanged(snapshot) {
            return (
                this.formData.situation !== snapshot.situation ||
                this.formData.anxiety_thought !== snapshot.anxiety_thought ||
                this.formData.actual_outcome !== snapshot.actual_outcome
            );
        },

        // 30秒ごとの自動保存チェック
        async checkAndAutoSave() {
            if (
                this.formData.situation.trim() &&
                this.hasChangedFromPreviousSnapshot() &&
                !this.submitting &&
                !this.autoSaving &&
                !this.isSaving &&
                !this.floatingSaving
            ) {
                await this.performAutoSave();
            }

            this.takeSnapshot();
        },

        // 共通の保存処理（排他制御付き）
        async performSave(isManual = false) {
            // 既に保存中の場合はスキップ（重複保存防止）
            if (this.isSaving) {
                console.log('保存処理中のためスキップ');
                return false;
            }

            this.isSaving = true;
            try {
                if (this.itemId) {
                    // 既存の更新
                    const res = await fetch(`/api/anxiety-diaries/${this.itemId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        this.showSaveNotification(isManual);
                        return true;
                    } else {
                        // エラーレスポンスのハンドリング
                        const errorData = await res.json().catch(() => ({}));
                        const errorMessage = errorData.message || '保存に失敗しました';
                        this.showSaveError(errorMessage, isManual);
                        return false;
                    }
                } else {
                    // 新規作成
                    const res = await fetch('/api/anxiety-diaries', {
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
                        history.replaceState(null, '', `/anxiety-diaries/${this.itemId}/edit`);
                        this.showSaveNotification(isManual);
                        return true;
                    } else {
                        // エラーレスポンスのハンドリング
                        const errorData = await res.json().catch(() => ({}));
                        const errorMessage = errorData.message || '保存に失敗しました';
                        this.showSaveError(errorMessage, isManual);
                        return false;
                    }
                }
            } catch (error) {
                console.error(isManual ? '保存に失敗しました:' : '自動保存に失敗しました:', error);
                this.showSaveError('ネットワークエラーが発生しました', isManual);
                return false;
            } finally {
                this.isSaving = false;
            }
        },

        // 自動保存を実行
        async performAutoSave() {
            this.autoSaving = true;
            try {
                await this.performSave(false);
            } finally {
                this.autoSaving = false;
            }
        },

        // 手動保存（フローティングボタン用）
        async manualSave() {
            if (this.floatingSaving || this.isSaving || !this.isFormValid()) return;

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

        showSaveError(message, isManual = false) {
            // エラーメッセージをUIに表示
            this.error = message;
            // 3秒後にエラーメッセージを消す（自動保存の場合）
            if (!isManual) {
                setTimeout(() => {
                    if (this.error === message) {
                        this.error = '';
                    }
                }, 5000);
            }
        },

        async loadItem() {
            this.loading = true;
            try {
                const res = await fetch(`/api/anxiety-diaries/${this.itemId}`);
                if (res.ok) {
                    const item = await res.json();
                    this.formData.situation = item.situation || '';
                    this.formData.anxiety_thought = item.anxiety_thought || '';
                    this.formData.actual_outcome = item.actual_outcome || '';
                    this.formData.stressor_and_response_id = item.stressor_and_response_id || null;
                    if (item.stressor_and_response_id) {
                        this.hasTransferred = true;
                    }
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        isFormValid() {
            return this.formData.situation.trim();
        },

        async saveItem() {
            if (this.isEditMode) {
                await this.updateItem();
            } else {
                await this.createItem();
            }
        },

        async createItem() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = '状況を入力してください';
                return;
            }

            // 既に保存中の場合は待機
            if (this.isSaving) {
                this.error = '保存処理中です。しばらくお待ちください。';
                return;
            }

            this.submitting = true;
            this.isSaving = true;
            try {
                // 自動保存で既にレコードが作成されている場合は更新として処理
                if (this.itemId) {
                    const res = await fetch(`/api/anxiety-diaries/${this.itemId}`, {
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
                } else {
                    const res = await fetch('/api/anxiety-diaries', {
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
                }

                window.location.href = '/anxiety-diaries/list';
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
                this.isSaving = false;
            }
        },

        async updateItem() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = '状況を入力してください';
                return;
            }

            // 既に保存中の場合は待機
            if (this.isSaving) {
                this.error = '保存処理中です。しばらくお待ちください。';
                return;
            }

            this.submitting = true;
            this.isSaving = true;
            try {
                const res = await fetch(`/api/anxiety-diaries/${this.itemId}`, {
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

                window.location.href = `/anxiety-diaries/${this.itemId}`;
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
                this.isSaving = false;
            }
        }
    };
}
</script>
@endsection

@extends('layouts.app')

@section('title', 'コラム法')
@section('page-title', 'コラム法')

@section('content')
<div x-data="columnApp({{ $columnId ?? 'null' }})" x-init="init()" x-cloak>
    <!-- 編集モード時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a :href="'/columns/' + columnId" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
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
                        選択すると「状況」「気分」「自動思考」に転記されます
                    </p>
                </div>

                <!-- コンテンツ -->
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="item in stressorAndResponses" :key="item.id">
                        <div
                            @click="showTransferConfirm(item)"
                            class="px-6 py-4 border-b border-gray-100 hover:bg-indigo-50 cursor-pointer transition-colors group"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <!-- ストレッサー（状況に転記） -->
                                    <div class="mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 mb-1">
                                            状況へ
                                        </span>
                                        <p class="text-sm text-gray-800 line-clamp-2" x-text="item.stressor"></p>
                                    </div>
                                    <!-- 気分・感情 -->
                                    <div class="mb-2" x-show="item.mood">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 mb-1">
                                            気分へ
                                        </span>
                                        <p class="text-sm text-gray-600 line-clamp-1" x-text="item.mood"></p>
                                    </div>
                                    <!-- 認知（自動思考に転記） -->
                                    <div x-show="item.cognition">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 mb-1">
                                            自動思考へ
                                        </span>
                                        <p class="text-sm text-gray-600 line-clamp-1" x-text="item.cognition"></p>
                                    </div>
                                    <!-- 作成日時 -->
                                    <p class="text-xs text-gray-400 mt-2" x-text="formatDate(item.created_at)"></p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 group-hover:bg-indigo-200 transition-colors">
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
                        「状況」「気分」「自動思考」に内容が転記されます。
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
        <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">読み込み中...</p>
    </div>

    <!-- コピー成功トースト -->
    <div
        x-show="showCopyToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2"
    >
        <span>📋</span>
        <span>コピーしました！</span>
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
        class="fixed top-16 right-4 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-md z-40 flex items-center gap-2"
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
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center hover:from-emerald-600 hover:to-teal-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed z-30"
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

    <!-- コラム作成/編集フォーム -->
    <div x-show="!loading || !isEditMode">
    <form @submit.prevent="saveColumn()">
        <div class="space-y-5">
            <!-- (1) 状況 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">1</span>
                    状況 <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal ml-1">気持ちが動揺したときの一場面</span>
                </label>
                <textarea
                    x-model="newColumn.situation"
                    rows="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="例：会議で自分の意見を否定された"
                    maxlength="1000"
                    required
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newColumn.situation.length + '/1000'"></div>
            </div>

            <!-- タグセクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    🏷️ タグ
                    <span class="text-gray-400 font-normal text-sm">（任意・複数選択可）</span>
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    この状況に関連するカテゴリーを選択してください
                </p>

                <!-- タグ選択UI -->
                <div class="flex flex-wrap gap-2">
                    <template x-for="tag in availableTags" :key="tag.id">
                        <button
                            type="button"
                            @click="toggleTag(tag.id)"
                            class="px-3 py-1.5 text-sm rounded-full border transition-all"
                            :class="isTagSelected(tag.id)
                                ? 'bg-emerald-500 text-white border-emerald-500'
                                : 'bg-white text-gray-700 border-gray-300 hover:border-emerald-400 hover:bg-emerald-50'"
                            x-text="tag.name"
                        ></button>
                    </template>
                </div>
            </div>

            <!-- (2) 気分 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">2</span>
                    気分
                    <span class="text-gray-400 font-normal ml-1">そのときの気持ち</span>
                    <!-- 感情リストトグルボタン -->
                    <button
                        type="button"
                        @click="showMoodEmotions = !showMoodEmotions"
                        class="ml-2 inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full transition-all"
                        :class="showMoodEmotions ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        感情リスト
                    </button>
                </label>

                <!-- 感情リスト（2番用） -->
                <div
                    x-show="showMoodEmotions"
                    x-collapse
                    class="mb-2 p-3 bg-emerald-50 border border-emerald-200 rounded-lg"
                >
                    <p class="text-xs text-emerald-600 mb-2">タップして感情を追加できます</p>

                    <!-- ネガティブエリア -->
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-gray-500 mb-1.5">😔 ネガティブ</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="emotion in negativeEmotions" :key="emotion">
                                <button
                                    type="button"
                                    @click="addEmotionToMood(emotion)"
                                    class="px-2.5 py-1 text-sm bg-white border border-gray-300 rounded-full hover:bg-gray-100 hover:border-gray-400 transition-all"
                                    x-text="emotion"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <!-- ポジティブエリア -->
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-1.5">😊 ポジティブ</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="emotion in positiveEmotions" :key="emotion">
                                <button
                                    type="button"
                                    @click="addEmotionToMood(emotion)"
                                    class="px-2.5 py-1 text-sm bg-white border border-emerald-300 rounded-full hover:bg-emerald-100 hover:border-emerald-400 transition-all"
                                    x-text="emotion"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>

                <textarea
                    x-model="newColumn.mood"
                    rows="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="例：悲しい(80%) 恥ずかしい(60%)"
                    maxlength="500"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newColumn.mood.length + '/500'"></div>
            </div>

            <!-- (3) 自動思考 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-1">3</span>
                    自動思考
                    <span class="text-gray-400 font-normal ml-1">そのとき頭に浮かんだこと</span>
                </label>
                <textarea
                    x-model="newColumn.automatic_thought"
                    rows="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="例：自分は仕事ができない人間だ"
                    maxlength="1000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newColumn.automatic_thought.length + '/1000'"></div>
            </div>

            <!-- (4) 根拠 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-500 text-white text-xs font-bold mr-1">4</span>
                    根拠
                    <span class="text-gray-400 font-normal ml-1">自動思考を裏付ける具体的な事実</span>
                </label>
                <textarea
                    x-model="newColumn.evidence"
                    rows="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                    placeholder="例：提案が採用されなかった"
                    maxlength="1000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newColumn.evidence.length + '/1000'"></div>
            </div>

            <!-- (5) 反証 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-cyan-600 text-white text-xs font-bold mr-1">5</span>
                    反証
                    <span class="text-gray-400 font-normal ml-1">自動思考と反対の事実</span>
                </label>
                <textarea
                    x-model="newColumn.counter_evidence"
                    rows="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all"
                    placeholder="例：先月の提案は採用されて好評だった"
                    maxlength="1000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newColumn.counter_evidence.length + '/1000'"></div>
            </div>

            <!-- (6) 適応的思考 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500 text-white text-xs font-bold mr-1">6</span>
                    適応的思考
                    <span class="text-gray-400 font-normal ml-1">バランスのよい考え</span>
                </label>
                <textarea
                    x-model="newColumn.adaptive_thought"
                    rows="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                    placeholder="例：今回は合わなかっただけで、自分には良い提案もできる"
                    maxlength="1000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newColumn.adaptive_thought.length + '/1000'"></div>
            </div>

            <!-- (7) いまの気分 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-lime-500 text-white text-xs font-bold mr-1">7</span>
                    いまの気分
                    <span class="text-gray-400 font-normal ml-1">コラムを書き終えた後の気持ち</span>
                    <!-- 感情リストトグルボタン -->
                    <button
                        type="button"
                        @click="showCurrentMoodEmotions = !showCurrentMoodEmotions"
                        class="ml-2 inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full transition-all"
                        :class="showCurrentMoodEmotions ? 'bg-lime-500 text-white' : 'bg-lime-100 text-lime-700 hover:bg-lime-200'"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        感情リスト
                    </button>
                </label>

                <!-- 感情リスト（7番用） -->
                <div
                    x-show="showCurrentMoodEmotions"
                    x-collapse
                    class="mb-2 p-3 bg-lime-50 border border-lime-200 rounded-lg"
                >
                    <p class="text-xs text-lime-600 mb-2">タップして感情を追加できます</p>

                    <!-- ネガティブエリア -->
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-gray-500 mb-1.5">😔 ネガティブ</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="emotion in negativeEmotions" :key="emotion">
                                <button
                                    type="button"
                                    @click="addEmotionToCurrentMood(emotion)"
                                    class="px-2.5 py-1 text-sm bg-white border border-gray-300 rounded-full hover:bg-gray-100 hover:border-gray-400 transition-all"
                                    x-text="emotion"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <!-- ポジティブエリア -->
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-1.5">😊 ポジティブ</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="emotion in positiveEmotions" :key="emotion">
                                <button
                                    type="button"
                                    @click="addEmotionToCurrentMood(emotion)"
                                    class="px-2.5 py-1 text-sm bg-white border border-lime-300 rounded-full hover:bg-lime-100 hover:border-lime-400 transition-all"
                                    x-text="emotion"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>

                <textarea
                    x-model="newColumn.current_mood"
                    rows="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all"
                    placeholder="例：悲しい(40%) 少し楽になった"
                    maxlength="500"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newColumn.current_mood.length + '/500'"></div>
            </div>

            <!-- 備考 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-500 text-white text-xs font-bold mr-1">📝</span>
                    備考
                    <span class="text-gray-400 font-normal ml-1">その他メモしておきたいこと</span>
                </label>
                <textarea
                    x-model="newColumn.notes"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all"
                    placeholder="例：次回から気をつけたいこと、気づいたパターンなど"
                    maxlength="2000"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newColumn.notes.length + '/2000'"></div>
            </div>

            <!-- エラーメッセージ -->
            <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>

            <!-- ボタンエリア -->
            <div class="space-y-3">
                <!-- 送信ボタン -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-4 px-6 rounded-xl font-semibold hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="submitting || !isFormValid()"
                >
                    <span x-show="!submitting && !isEditMode" class="flex items-center justify-center gap-2">
                        ✨ コラムを保存
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

                <!-- コピーボタン -->
                <button
                    type="button"
                    @click="copyToClipboard()"
                    class="w-full bg-white border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all flex items-center justify-center gap-2"
                    :disabled="!hasAnyContent()"
                    :class="{ 'opacity-50 cursor-not-allowed': !hasAnyContent() }"
                >
                    📋 入力内容をコピー
                </button>
            </div>
        </div>
    </form>
    </div>
</div>

<script>
function columnApp(columnId) {
    return {
        columnId: columnId,
        isEditMode: columnId !== null,
        newColumn: {
            situation: '',
            mood: '',
            automatic_thought: '',
            evidence: '',
            counter_evidence: '',
            adaptive_thought: '',
            current_mood: '',
            notes: '',
            tag_ids: []
        },
        loading: false,
        submitting: false,
        error: '',
        showCopyToast: false,
        showAutoSaveToast: false,
        showManualSaveToast: false,
        floatingSaving: false,

        // タグ一覧
        availableTags: [],

        // 自動保存用
        autoSaveSnapshots: [], // 30秒ごとのスナップショット（直近2つ分を保持）
        autoSaveInterval: null,
        autoSaving: false,

        // 感情リストの表示状態
        showMoodEmotions: false,
        showCurrentMoodEmotions: false,

        // ストレッサーとストレス反応からの転記機能
        stressorAndResponses: [],
        showStressorModal: false,
        showTransferToast: false,
        hasTransferred: false, // 転記済みフラグ
        selectedStressorItem: null, // 確認ダイアログ用に選択されたアイテム
        showTransferConfirmModal: false, // 転記確認ダイアログの表示状態

        // ネガティブ感情リスト
        negativeEmotions: [
            // 怒り系
            '怒り', 'イライラ', '腹立たしい', 'ムカつく', '憤り',
            // 悲しみ系
            '悲しい', '寂しい', '切ない', '虚しい', '孤独',
            // 不安・恐怖系
            '不安', '心配', '恐怖', '怖い', 'パニック', '焦り', '緊張',
            // 落ち込み系
            '落ち込み', '憂うつ', '絶望', '無力感', '疲労感',
            // 恥・罪悪感系
            '恥ずかしい', '罪悪感', '後悔', '自己嫌悪', '情けない',
            // 嫉妬・羨望系
            '嫉妬', '羨ましい', '妬ましい', '劣等感',
            // その他
            '困惑', '戸惑い', 'もどかしい', '退屈', '不満', '失望',
            '複雑', 'モヤモヤ'
        ],
        // ポジティブ感情リスト
        positiveEmotions: [
            '嬉しい', '楽しい', '幸せ', 'ワクワク', '期待',
            '安心', 'ホッとした', '満足', '達成感', '充実感',
            '感謝', '愛情', '親しみ', '誇らしい', '自信',
            '驚き', 'スッキリ'
        ],

        async init() {
            // タグ一覧を取得
            await this.loadTags();

            // ストレッサーとストレス反応一覧を取得
            await this.loadStressorAndResponses();

            if (this.isEditMode) {
                await this.loadColumn();
            }

            // 初期スナップショットを取得
            this.takeSnapshot();

            // 30秒ごとに自動保存チェック
            this.autoSaveInterval = setInterval(() => {
                this.checkAndAutoSave();
            }, 30000);
        },

        async loadTags() {
            try {
                const res = await fetch('/api/tags');
                if (res.ok) {
                    this.availableTags = await res.json();
                }
            } catch (error) {
                console.error('タグの取得に失敗しました:', error);
            }
        },

        toggleTag(tagId) {
            const index = this.newColumn.tag_ids.indexOf(tagId);
            if (index > -1) {
                this.newColumn.tag_ids.splice(index, 1);
            } else {
                this.newColumn.tag_ids.push(tagId);
            }
        },

        isTagSelected(tagId) {
            return this.newColumn.tag_ids.includes(tagId);
        },

        getTagName(tagId) {
            const tag = this.availableTags.find(t => t.id === tagId);
            return tag ? tag.name : '';
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
            this.newColumn.situation = this.selectedStressorItem.stressor || '';
            // 気分 ← 気分・感情
            this.newColumn.mood = this.selectedStressorItem.mood || '';
            // 自動思考 ← 認知（自動思考）
            this.newColumn.automatic_thought = this.selectedStressorItem.cognition || '';
            // タグ ← タグ
            this.newColumn.tag_ids = this.selectedStressorItem.tag_ids || [];

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

        // ストレッサーとストレス反応のデータを転記（後方互換性のため残す）
        applyStressorData(item) {
            this.showTransferConfirm(item);
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
                situation: this.newColumn.situation,
                mood: this.newColumn.mood,
                automatic_thought: this.newColumn.automatic_thought,
                evidence: this.newColumn.evidence,
                counter_evidence: this.newColumn.counter_evidence,
                adaptive_thought: this.newColumn.adaptive_thought,
                current_mood: this.newColumn.current_mood,
                notes: this.newColumn.notes,
                tag_ids: JSON.stringify(this.newColumn.tag_ids)
            };
            this.autoSaveSnapshots.push(snapshot);

            // 直近2つ分のみ保持（60秒前の値と比較するため）
            if (this.autoSaveSnapshots.length > 2) {
                this.autoSaveSnapshots.shift();
            }
        },

        // 60秒前のスナップショットと現在の値を比較
        hasChangedFromPreviousSnapshot() {
            // 2回分のスナップショットがない場合（まだ60秒経っていない）
            if (this.autoSaveSnapshots.length < 2) {
                // 1つ目のスナップショットと比較
                if (this.autoSaveSnapshots.length === 1) {
                    return this.hasValueChanged(this.autoSaveSnapshots[0]);
                }
                return false;
            }

            // 60秒前（2回前）のスナップショットと比較
            const oldSnapshot = this.autoSaveSnapshots[0];
            return this.hasValueChanged(oldSnapshot);
        },

        // 指定されたスナップショットと現在の値を比較
        hasValueChanged(snapshot) {
            return (
                this.newColumn.situation !== snapshot.situation ||
                this.newColumn.mood !== snapshot.mood ||
                this.newColumn.automatic_thought !== snapshot.automatic_thought ||
                this.newColumn.evidence !== snapshot.evidence ||
                this.newColumn.counter_evidence !== snapshot.counter_evidence ||
                this.newColumn.adaptive_thought !== snapshot.adaptive_thought ||
                this.newColumn.current_mood !== snapshot.current_mood ||
                this.newColumn.notes !== snapshot.notes ||
                JSON.stringify(this.newColumn.tag_ids) !== snapshot.tag_ids
            );
        },

        // 30秒ごとの自動保存チェック
        async checkAndAutoSave() {
            // 条件チェック：
            // 1. 「状況」が入力済み
            // 2. 1分前の値から変更がある
            // 3. 現在保存中でない
            if (
                this.newColumn.situation.trim() &&
                this.hasChangedFromPreviousSnapshot() &&
                !this.submitting &&
                !this.autoSaving
            ) {
                await this.performAutoSave();
            }

            // 新しいスナップショットを取得
            this.takeSnapshot();
        },

        // 共通の保存処理
        async performSave(isManual = false) {
            try {
                if (this.columnId) {
                    // 既存コラムの更新
                    const res = await fetch(`/api/columns/${this.columnId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.newColumn)
                    });

                    if (res.ok) {
                        this.showSaveNotification(isManual);
                    }
                } else {
                    // 新規作成
                    const res = await fetch('/api/columns', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.newColumn)
                    });

                    if (res.ok) {
                        const data = await res.json();
                        // 新規作成後は編集モードに切り替え（以降の自動保存は更新になる）
                        this.columnId = data.id;
                        this.isEditMode = true;
                        // URLを編集ページに変更（リロードなし）
                        history.replaceState(null, '', `/columns/${this.columnId}/edit`);
                        this.showSaveNotification(isManual);
                    }
                }
            } catch (error) {
                console.error(isManual ? '保存に失敗しました:' : '自動保存に失敗しました:', error);
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

        async loadColumn() {
            this.loading = true;
            try {
                const res = await fetch(`/api/columns/${this.columnId}`);
                if (res.ok) {
                    const column = await res.json();
                    this.newColumn.situation = column.situation || '';
                    this.newColumn.mood = column.mood || '';
                    this.newColumn.automatic_thought = column.automatic_thought || '';
                    this.newColumn.evidence = column.evidence || '';
                    this.newColumn.counter_evidence = column.counter_evidence || '';
                    this.newColumn.adaptive_thought = column.adaptive_thought || '';
                    this.newColumn.current_mood = column.current_mood || '';
                    this.newColumn.notes = column.notes || '';
                    this.newColumn.tag_ids = column.tag_ids || [];
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        // 2番「気分」に感情を追加
        addEmotionToMood(emotion) {
            if (this.newColumn.mood.trim().length > 0) {
                this.newColumn.mood += '\n' + emotion;
            } else {
                this.newColumn.mood = emotion;
            }
        },

        // 7番「いまの気分」に感情を追加
        addEmotionToCurrentMood(emotion) {
            if (this.newColumn.current_mood.trim().length > 0) {
                this.newColumn.current_mood += '\n' + emotion;
            } else {
                this.newColumn.current_mood = emotion;
            }
        },

        isFormValid() {
            return this.newColumn.situation.trim();
        },

        hasAnyContent() {
            return this.newColumn.situation.trim() ||
                   this.newColumn.mood.trim() ||
                   this.newColumn.automatic_thought.trim() ||
                   this.newColumn.evidence.trim() ||
                   this.newColumn.counter_evidence.trim() ||
                   this.newColumn.adaptive_thought.trim() ||
                   this.newColumn.current_mood.trim();
        },

        generateCopyText() {
            const sections = [];
            sections.push('【コラム法】');
            sections.push('');

            sections.push('■ 状況');
            sections.push(this.newColumn.situation.trim() || '未入力');
            sections.push('');

            sections.push('■ 気分');
            sections.push(this.newColumn.mood.trim() || '未入力');
            sections.push('');

            sections.push('■ 自動思考');
            sections.push(this.newColumn.automatic_thought.trim() || '未入力');
            sections.push('');

            sections.push('■ 根拠');
            sections.push(this.newColumn.evidence.trim() || '未入力');
            sections.push('');

            sections.push('■ 反証');
            sections.push(this.newColumn.counter_evidence.trim() || '未入力');
            sections.push('');

            sections.push('■ 適応的思考');
            sections.push(this.newColumn.adaptive_thought.trim() || '未入力');
            sections.push('');

            sections.push('■ いまの気分');
            sections.push(this.newColumn.current_mood.trim() || '未入力');

            return sections.join('\n').trim();
        },

        async copyToClipboard() {
            const text = this.generateCopyText();

            try {
                await navigator.clipboard.writeText(text);
                this.showCopyToast = true;
                setTimeout(() => {
                    this.showCopyToast = false;
                }, 2000);
            } catch (err) {
                // フォールバック: 古いブラウザ対応
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    this.showCopyToast = true;
                    setTimeout(() => {
                        this.showCopyToast = false;
                    }, 2000);
                } catch (err) {
                    console.error('コピーに失敗しました:', err);
                }
                document.body.removeChild(textArea);
            }
        },

        async saveColumn() {
            if (this.isEditMode) {
                await this.updateColumn();
            } else {
                await this.createColumn();
            }
        },

        async createColumn() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = '状況を入力してください';
                return;
            }

            this.submitting = true;
            try {
                const res = await fetch('/api/columns', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newColumn)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                // 保存成功したら一覧ページに遷移
                window.location.href = '/columns/list';
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        },

        async updateColumn() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = '状況を入力してください';
                return;
            }

            this.submitting = true;
            try {
                const res = await fetch(`/api/columns/${this.columnId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newColumn)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                // 更新成功したら詳細ページに遷移
                window.location.href = `/columns/${this.columnId}`;
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection

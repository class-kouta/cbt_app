@extends('layouts.app')

@section('title', 'ストレッサーとストレス反応')
@section('page-title', 'ストレッサーとストレス反応')

@section('content')
<div x-data="stressorApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <!-- 編集モード時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a :href="'/stressor-and-responses/' + itemId" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
            ← 詳細に戻る
        </a>
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

    <!-- フォーム -->
    <div x-show="!loading || !isEditMode">
    <form @submit.prevent="saveItem()">
        <div class="space-y-5">
            <!-- ストレッサーセクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    ストレッサー
                    <span class="text-red-500 text-sm">*</span>
                </h3>
                <textarea
                    x-model="formData.stressor"
                    rows="6"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="例：仕事の締め切りが重なった"
                    maxlength="1000"
                    required
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="formData.stressor.length + '/1000'"></div>
            </div>

            <!-- タグセクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    🏷️ タグ
                    <span class="text-gray-400 font-normal text-sm">（任意・複数選択可）</span>
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    このストレッサーに関連するカテゴリーを選択してください
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

            <!-- ストレス反応セクション -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-base font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    ストレス反応
                    <span class="text-gray-400 font-normal text-sm">（任意）</span>
                </h3>
                
                <div class="space-y-4">
                    <!-- 認知（自動思考） -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            認知（自動思考）
                        </label>
                        <textarea
                            x-model="formData.cognition"
                            rows="8"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                            placeholder="例：もう間に合わない、失敗したらどうしよう"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="formData.cognition.length + '/1000'"></div>
                    </div>

                    <!-- 気分・感情 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            気分・感情
                            <!-- 感情リストトグルボタン -->
                            <button
                                type="button"
                                @click="showMoodEmotions = !showMoodEmotions"
                                class="ml-2 inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full transition-all"
                                :class="showMoodEmotions ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                感情リスト
                            </button>
                        </label>

                        <!-- 感情リスト -->
                        <div
                            x-show="showMoodEmotions"
                            x-collapse
                            class="mb-2 p-3 bg-blue-50 border border-blue-200 rounded-lg"
                        >
                            <p class="text-xs text-blue-600 mb-2">タップして感情を追加できます</p>

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
                                            class="px-2.5 py-1 text-sm bg-white border border-blue-300 rounded-full hover:bg-blue-100 hover:border-blue-400 transition-all"
                                            x-text="emotion"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <textarea
                            x-model="formData.mood"
                            rows="8"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="例：不安(80%)、焦り(70%)"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="formData.mood.length + '/1000'"></div>
                    </div>

                    <!-- 身体反応 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            身体反応
                        </label>
                        <textarea
                            x-model="formData.body_reaction"
                            rows="4"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                            placeholder="例：頭痛、肩こり、動悸、胃の痛み"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="formData.body_reaction.length + '/1000'"></div>
                    </div>

                    <!-- 行動 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            行動
                        </label>
                        <textarea
                            x-model="formData.behavior"
                            rows="4"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                            placeholder="例：仕事を先延ばしにした、イライラして周りに当たった"
                            maxlength="1000"
                        ></textarea>
                        <div class="text-xs text-gray-400 text-right" x-text="formData.behavior.length + '/1000'"></div>
                    </div>
                </div>
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
function stressorApp(itemId) {
    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        formData: {
            stressor: '',
            cognition: '',
            mood: '',
            body_reaction: '',
            behavior: '',
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
        autoSaveSnapshots: [],
        autoSaveInterval: null,
        autoSaving: false,

        // 感情リストの表示状態
        showMoodEmotions: false,

        // ネガティブ感情リスト
        negativeEmotions: [
            '怒り', 'イライラ', '腹立たしい', 'ムカつく', '憤り',
            '悲しい', '寂しい', '切ない', '虚しい', '孤独',
            '不安', '心配', '恐怖', '怖い', 'パニック', '焦り', '緊張',
            '落ち込み', '憂うつ', '絶望', '無力感', '疲労感',
            '恥ずかしい', '罪悪感', '後悔', '自己嫌悪', '情けない',
            '嫉妬', '羨ましい', '妬ましい', '劣等感',
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

        async loadTags() {
            try {
                const res = await apiFetch('/api/tags');
                if (res.ok) {
                    this.availableTags = await res.json();
                }
            } catch (error) {
                console.error('タグの取得に失敗しました:', error);
            }
        },

        toggleTag(tagId) {
            const index = this.formData.tag_ids.indexOf(tagId);
            if (index > -1) {
                this.formData.tag_ids.splice(index, 1);
            } else {
                this.formData.tag_ids.push(tagId);
            }
        },

        isTagSelected(tagId) {
            return this.formData.tag_ids.includes(tagId);
        },

        getTagName(tagId) {
            const tag = this.availableTags.find(t => t.id === tagId);
            return tag ? tag.name : '';
        },

        takeSnapshot() {
            const snapshot = {
                stressor: this.formData.stressor,
                cognition: this.formData.cognition,
                mood: this.formData.mood,
                body_reaction: this.formData.body_reaction,
                behavior: this.formData.behavior,
                tag_ids: JSON.stringify(this.formData.tag_ids)
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
                this.formData.stressor !== snapshot.stressor ||
                this.formData.cognition !== snapshot.cognition ||
                this.formData.mood !== snapshot.mood ||
                this.formData.body_reaction !== snapshot.body_reaction ||
                this.formData.behavior !== snapshot.behavior ||
                JSON.stringify(this.formData.tag_ids) !== snapshot.tag_ids
            );
        },

        async checkAndAutoSave() {
            if (
                this.formData.stressor.trim() &&
                this.hasChangedFromPreviousSnapshot() &&
                !this.submitting &&
                !this.autoSaving
            ) {
                await this.performAutoSave();
            }
            this.takeSnapshot();
        },

        // 共通の保存処理
        async performSave(isManual = false) {
            try {
                if (this.itemId) {
                    // 既存データの更新
                    const res = await apiFetch(`/api/stressor-and-responses/${this.itemId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        this.showSaveNotification(isManual);
                    }
                } else {
                    // 新規作成
                    const res = await apiFetch('/api/stressor-and-responses', {
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
                        history.replaceState(null, '', `/stressor-and-responses/${this.itemId}/edit`);
                        this.showSaveNotification(isManual);
                    }
                }
            } catch (error) {
                console.error(isManual ? '保存に失敗しました:' : '自動保存に失敗しました:', error);
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

        async loadItem() {
            this.loading = true;
            try {
                const res = await apiFetch(`/api/stressor-and-responses/${this.itemId}`);
                if (res.ok) {
                    const item = await res.json();
                    this.formData.stressor = item.stressor || '';
                    this.formData.cognition = item.cognition || '';
                    this.formData.mood = item.mood || '';
                    this.formData.body_reaction = item.body_reaction || '';
                    this.formData.behavior = item.behavior || '';
                    this.formData.tag_ids = item.tag_ids || [];
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        addEmotionToMood(emotion) {
            if (this.formData.mood.trim().length > 0) {
                this.formData.mood += '\n' + emotion;
            } else {
                this.formData.mood = emotion;
            }
        },

        isFormValid() {
            return this.formData.stressor.trim();
        },

        hasAnyContent() {
            return this.formData.stressor.trim() ||
                   this.formData.cognition.trim() ||
                   this.formData.mood.trim() ||
                   this.formData.body_reaction.trim() ||
                   this.formData.behavior.trim();
        },

        generateCopyText() {
            const sections = [];
            sections.push('【ストレッサーとストレス反応】');
            sections.push('');

            sections.push('■ ストレッサー');
            sections.push(this.formData.stressor.trim() || '未入力');
            sections.push('');

            sections.push('■ 認知（自動思考）');
            sections.push(this.formData.cognition.trim() || '未入力');
            sections.push('');

            sections.push('■ 気分・感情');
            sections.push(this.formData.mood.trim() || '未入力');
            sections.push('');

            sections.push('■ 身体反応');
            sections.push(this.formData.body_reaction.trim() || '未入力');
            sections.push('');

            sections.push('■ 行動');
            sections.push(this.formData.behavior.trim() || '未入力');

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
                this.error = 'ストレッサーを入力してください';
                return;
            }

            this.submitting = true;
            try {
                const res = await apiFetch('/api/stressor-and-responses', {
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

                window.location.href = '/stressor-and-responses/list';
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        },

        async updateItem() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = 'ストレッサーを入力してください';
                return;
            }

            this.submitting = true;
            try {
                const res = await apiFetch(`/api/stressor-and-responses/${this.itemId}`, {
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

                window.location.href = `/stressor-and-responses/${this.itemId}`;
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection

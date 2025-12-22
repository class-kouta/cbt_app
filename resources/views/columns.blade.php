@extends('layouts.app')

@section('title', 'コラム法')

@section('content')
<div x-data="columnApp({{ $columnId ?? 'null' }})" x-init="init()" x-cloak>
    <!-- 編集モード時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a :href="'/columns/' + columnId" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
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
            notes: ''
        },
        loading: false,
        submitting: false,
        error: '',
        showCopyToast: false,

        // 感情リストの表示状態
        showMoodEmotions: false,
        showCurrentMoodEmotions: false,

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
            if (this.isEditMode) {
                await this.loadColumn();
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

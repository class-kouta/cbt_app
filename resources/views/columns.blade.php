@extends('layouts.app')

@section('title', 'コラム法')

@section('content')
<div x-data="columnApp()" x-cloak>
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
    <!-- 新規コラム作成フォーム -->
    <form @submit.prevent="createColumn()">
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
                    rows="2"
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
                </label>
                <textarea
                    x-model="newColumn.mood"
                    rows="2"
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
                    rows="2"
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
                    rows="2"
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
                    rows="2"
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
                    rows="2"
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
                </label>
                <textarea
                    x-model="newColumn.current_mood"
                    rows="2"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all"
                    placeholder="例：悲しい(40%) 少し楽になった"
                    maxlength="500"
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newColumn.current_mood.length + '/500'"></div>
            </div>

            <!-- エラーメッセージ -->
            <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3" x-text="error"></div>

            <!-- ボタンエリア -->
            <div class="space-y-3">
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

                <!-- 送信ボタン -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white py-4 px-6 rounded-xl font-semibold hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="loading || !isFormValid()"
                >
                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                        ✨ コラムを保存
                    </span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
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

<script>
function columnApp() {
    return {
        newColumn: {
            situation: '',
            mood: '',
            automatic_thought: '',
            evidence: '',
            counter_evidence: '',
            adaptive_thought: '',
            current_mood: ''
        },
        loading: false,
        error: '',
        showCopyToast: false,

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

            if (this.newColumn.situation.trim()) {
                sections.push('■ 状況');
                sections.push(this.newColumn.situation.trim());
                sections.push('');
            }

            if (this.newColumn.mood.trim()) {
                sections.push('■ 気分');
                sections.push(this.newColumn.mood.trim());
                sections.push('');
            }

            if (this.newColumn.automatic_thought.trim()) {
                sections.push('■ 自動思考');
                sections.push(this.newColumn.automatic_thought.trim());
                sections.push('');
            }

            if (this.newColumn.evidence.trim()) {
                sections.push('■ 根拠');
                sections.push(this.newColumn.evidence.trim());
                sections.push('');
            }

            if (this.newColumn.counter_evidence.trim()) {
                sections.push('■ 反証');
                sections.push(this.newColumn.counter_evidence.trim());
                sections.push('');
            }

            if (this.newColumn.adaptive_thought.trim()) {
                sections.push('■ 適応的思考');
                sections.push(this.newColumn.adaptive_thought.trim());
                sections.push('');
            }

            if (this.newColumn.current_mood.trim()) {
                sections.push('■ いまの気分');
                sections.push(this.newColumn.current_mood.trim());
                sections.push('');
            }

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

        async createColumn() {
            this.error = '';

            if (!this.isFormValid()) {
                this.error = '状況を入力してください';
                return;
            }

            this.loading = true;
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
                this.loading = false;
            }
        }
    };
}
</script>
@endsection

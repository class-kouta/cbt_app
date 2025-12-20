@extends('layouts.app')

@section('title', '筆記開示')

@section('content')
<div x-data="writingDisclosureApp()" x-init="init()" x-cloak>
    <!-- 説明 -->
    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-4">
        <p class="text-emerald-800 text-sm">
            💭 頭の中でぐるぐる回っている考えを書き出して、外に出してみましょう。書くことで気持ちが整理されることがあります。
        </p>
    </div>

    <!-- 過去の記録へのリンク -->
    <div class="flex justify-end mb-4">
        <a href="/writing-disclosures/list" class="text-teal-600 hover:text-teal-800 text-sm font-medium transition-colors flex items-center gap-1">
            📋 過去の記録を見る →
        </a>
    </div>

    <!-- 新規筆記開示作成フォーム -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form @submit.prevent="createWritingDisclosure()">
            <div class="space-y-4">
                <!-- メモ内容 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">今、頭の中にあることを書き出してみて</label>
                    <textarea
                        x-model="newContent"
                        rows="18"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="何でも自由に書いてください..."
                        maxlength="10000"
                        required
                    ></textarea>
                    <div class="text-xs text-gray-400 text-right" x-text="newContent.length + '/10000'"></div>
                </div>

                <!-- エラーメッセージ -->
                <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

                <!-- 成功メッセージ -->
                <div x-show="success" class="text-green-600 text-sm bg-green-50 border border-green-200 rounded-lg p-3">
                    ✨ 書き出しました！気持ちが少し軽くなったかな？
                </div>

                <!-- 送信ボタン -->
                <div>
                    <button
                        type="submit"
                        class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50"
                        :disabled="loading || !newContent.trim()"
                    >
                        <span x-show="!loading">書き出す 📝</span>
                        <span x-show="loading">保存中...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function writingDisclosureApp() {
    return {
        newContent: '',
        loading: false,
        error: '',
        success: false,

        init() {
            // 初期化
        },

        async createWritingDisclosure() {
            this.error = '';
            this.success = false;

            if (!this.newContent.trim()) {
                this.error = '内容を入力してください';
                return;
            }

            this.loading = true;
            try {
                const res = await fetch('/api/writing-disclosures', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.newContent
                    })
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                this.newContent = '';
                this.success = true;
                
                // 3秒後に成功メッセージを消す
                setTimeout(() => {
                    this.success = false;
                }, 3000);
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endsection

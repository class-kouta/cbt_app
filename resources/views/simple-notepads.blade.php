@extends('layouts.app')

@section('title', 'メモ帳')

@section('content')
<div x-data="simpleNotepadApp()" x-init="init()" x-cloak>
    <!-- 説明 -->
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
        <p class="text-amber-800 text-sm">
            📝 各機能に当てはまらないけど、思い浮かんだ思考をただ外在化したい時に。自由にメモを残しましょう。
        </p>
    </div>

    <!-- 新規メモ帳作成フォーム -->
    <form @submit.prevent="createSimpleNotepad()">
        <div class="space-y-4">
            <!-- メモ内容 -->
            <div>
                <textarea
                    x-model="newContent"
                    rows="18"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                    placeholder="なんでも自由に書いてください..."
                    maxlength="10000"
                    required
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newContent.length + '/10000'"></div>
            </div>

            <!-- エラーメッセージ -->
            <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

            <!-- 成功メッセージ -->
            <div x-show="success" class="text-green-600 text-sm bg-green-50 border border-green-200 rounded-lg p-3">
                ✨ メモを保存しました！
            </div>

            <!-- 送信ボタン -->
            <div>
                <button
                    type="submit"
                    class="w-full bg-amber-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-amber-600 transition-colors disabled:opacity-50"
                    :disabled="loading || !newContent.trim()"
                >
                    <span x-show="!loading">メモを保存 📝</span>
                    <span x-show="loading">保存中...</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function simpleNotepadApp() {
    return {
        newContent: '',
        loading: false,
        error: '',
        success: false,

        init() {
            // 初期化
        },

        async createSimpleNotepad() {
            this.error = '';
            this.success = false;

            if (!this.newContent.trim()) {
                this.error = '内容を入力してください';
                return;
            }

            this.loading = true;
            try {
                const res = await fetch('/api/simple-notepads', {
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

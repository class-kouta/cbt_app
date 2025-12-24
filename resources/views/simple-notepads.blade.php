@extends('layouts.app')

@section('title', 'メモ帳')
@section('page-title', 'メモ帳')

@section('content')
<div x-data="simpleNotepadApp({{ $itemId ?? 'null' }})" x-init="init()" x-cloak>
    <!-- 編集モード時のヘッダー -->
    <div class="flex justify-between items-center mb-4" x-show="isEditMode">
        <a :href="'/simple-notepads/' + itemId" class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
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

    <!-- 新規メモ帳作成/編集フォーム -->
    <div x-show="!loading || !isEditMode">
    <form @submit.prevent="saveSimpleNotepad()">
        <div class="space-y-4">
            <!-- メモ内容 -->
            <div>
                <textarea
                    x-model="newContent"
                    rows="18"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    placeholder="なんでも自由に書いてください..."
                    maxlength="10000"
                    required
                ></textarea>
                <div class="text-xs text-gray-400 text-right" x-text="newContent.length + '/10000'"></div>
            </div>

            <!-- エラーメッセージ -->
            <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

            <!-- 送信ボタン -->
            <div>
                <button
                    type="submit"
                    class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50"
                    :disabled="submitting || !newContent.trim()"
                >
                    <span x-show="!submitting && !isEditMode">メモを保存 📝</span>
                    <span x-show="!submitting && isEditMode">更新する 📝</span>
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
function simpleNotepadApp(itemId) {
    return {
        itemId: itemId,
        isEditMode: itemId !== null,
        newContent: '',
        loading: false,
        submitting: false,
        error: '',

        async init() {
            if (this.isEditMode) {
                await this.loadItem();
            }
        },

        async loadItem() {
            this.loading = true;
            try {
                const res = await fetch('/api/simple-notepads');
                const items = await res.json();
                const item = items.find(i => i.id === this.itemId);
                if (item) {
                    this.newContent = item.content || '';
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async saveSimpleNotepad() {
            if (this.isEditMode) {
                await this.updateSimpleNotepad();
            } else {
                await this.createSimpleNotepad();
            }
        },

        async createSimpleNotepad() {
            this.error = '';

            if (!this.newContent.trim()) {
                this.error = '内容を入力してください';
                return;
            }

            this.submitting = true;
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

                // 保存成功したら一覧ページに遷移
                window.location.href = '/simple-notepads/list';
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        },

        async updateSimpleNotepad() {
            this.error = '';

            if (!this.newContent.trim()) {
                this.error = '内容を入力してください';
                return;
            }

            this.submitting = true;
            try {
                const res = await fetch(`/api/simple-notepads/${this.itemId}`, {
                    method: 'PUT',
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

                // 更新成功したら詳細ページに遷移
                window.location.href = `/simple-notepads/${this.itemId}`;
            } catch (e) {
                this.error = e.message;
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection

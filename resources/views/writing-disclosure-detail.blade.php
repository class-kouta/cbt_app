@extends('layouts.app')

@section('title', '筆記開示 - 詳細')

@section('content')
<div x-data="writingDisclosureDetailApp()" x-init="init()" x-cloak>
    <!-- ヘッダー -->
    <div class="flex items-center justify-between mb-6">
        <a href="/writing-disclosures/list" class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors flex items-center gap-1">
            ← 一覧に戻る
        </a>
        <div class="flex gap-3" x-show="!editing && item">
            <button
                @click="startEdit()"
                class="text-xl hover:opacity-70 transition-opacity"
                title="編集"
            >
                ✏️
            </button>
            <button
                @click="deleteItem()"
                class="text-xl hover:opacity-70 transition-opacity"
                title="削除"
            >
                🗑️
            </button>
        </div>
    </div>

    <!-- ローディング -->
    <div x-show="loading" class="text-center py-12 text-gray-500">
        <p>読み込み中...</p>
    </div>

    <!-- エラー -->
    <div x-show="error && !loading" class="text-center py-12 text-gray-500">
        <p class="text-4xl mb-4">😢</p>
        <p x-text="error"></p>
        <a href="/writing-disclosures/list" class="text-orange-600 hover:text-orange-800 text-sm mt-4 inline-block">
            一覧に戻る →
        </a>
    </div>

    <!-- 詳細表示 -->
    <div x-show="item && !loading && !error">
        <!-- 表示モード -->
        <div x-show="!editing" class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-800 whitespace-pre-wrap break-words" x-text="item?.content"></p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-xs text-gray-400" x-text="item ? formatDate(item.created_at) : ''"></span>
            </div>
        </div>

        <!-- 編集モード -->
        <div x-show="editing" class="bg-white rounded-lg shadow-md p-6">
            <textarea
                x-model="editContent"
                rows="18"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                maxlength="10000"
            ></textarea>
            <div class="text-xs text-gray-400 text-right mb-4" x-text="editContent.length + '/10000'"></div>
            
            <div x-show="editError" class="text-red-500 text-sm mb-4" x-text="editError"></div>
            
            <div class="flex gap-2">
                <button
                    @click="saveEdit()"
                    class="flex-1 bg-orange-500 text-white py-2 px-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors disabled:opacity-50"
                    :disabled="saving || !editContent.trim()"
                >
                    <span x-show="!saving">保存</span>
                    <span x-show="saving">保存中...</span>
                </button>
                <button
                    @click="cancelEdit()"
                    class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg font-semibold hover:bg-gray-400 transition-colors"
                    :disabled="saving"
                >
                    キャンセル
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function writingDisclosureDetailApp() {
    return {
        item: null,
        loading: true,
        error: '',
        editing: false,
        editContent: '',
        editError: '',
        saving: false,

        async init() {
            await this.loadItem();
        },

        async loadItem() {
            this.loading = true;
            this.error = '';
            
            try {
                const id = {{ $itemId }};
                const res = await fetch('/api/writing-disclosures');
                const items = await res.json();
                
                this.item = items.find(item => item.id === id);
                
                if (!this.item) {
                    this.error = '記録が見つかりませんでした';
                }
            } catch (e) {
                this.error = 'データの読み込みに失敗しました';
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        startEdit() {
            this.editing = true;
            this.editContent = this.item.content;
            this.editError = '';
        },

        cancelEdit() {
            this.editing = false;
            this.editContent = '';
            this.editError = '';
        },

        async saveEdit() {
            if (!this.editContent.trim()) {
                this.editError = '内容を入力してください';
                return;
            }

            this.saving = true;
            this.editError = '';

            try {
                const res = await fetch(`/api/writing-disclosures/${this.item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.editContent
                    })
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                const updated = await res.json();
                this.item = updated;
                this.editing = false;
                this.editContent = '';
            } catch (e) {
                this.editError = e.message;
            } finally {
                this.saving = false;
            }
        },

        async deleteItem() {
            if (!confirm('この記録を削除しますか？')) return;

            try {
                await fetch(`/api/writing-disclosures/${this.item.id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                
                window.location.href = '/writing-disclosures/list';
            } catch (e) {
                console.error(e);
                alert('削除に失敗しました');
            }
        }
    };
}
</script>
@endsection

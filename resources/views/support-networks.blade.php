@extends('layouts.app')

@section('title', 'サポートネットワーク')
@section('page-title', 'サポートネットワーク')

@section('content')
<div x-data="supportNetworkApp()" x-init="init()" x-cloak>
    <!-- 新規サポートネットワーク作成フォーム -->
    <div class="mb-6">
        <form @submit.prevent="createSupportNetwork()">
            <div class="space-y-4">
                <!-- 名前 -->
                <div>
                    <input
                        type="text"
                        x-model="newSupportNetwork.name"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="サポート者の名前を入力..."
                        maxlength="100"
                        required
                    >
                    <div class="text-xs text-gray-400 text-right" x-text="newSupportNetwork.name.length + '/100'"></div>
                </div>

                <!-- エラーメッセージ -->
                <div x-show="error" class="text-red-500 text-sm" x-text="error"></div>

                <!-- 送信ボタン -->
                <div>
                    <button
                        type="submit"
                        class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50"
                        :disabled="loading || !newSupportNetwork.name.trim()"
                    >
                        <span x-show="!loading">追加</span>
                        <span x-show="loading">追加中...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- サポートネットワーク一覧 -->
    <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-2">
            合計: <span x-text="supportNetworks.length" class="font-bold"></span> 人
        </div>

        <template x-for="person in supportNetworks" :key="person.id">
            <div class="bg-white rounded-lg shadow-md p-4 transition-all hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <!-- 内容 -->
                    <div class="flex-1 min-w-0">
                        <!-- 編集モード -->
                        <div x-show="editingId === person.id">
                            <input
                                type="text"
                                x-model="editName"
                                x-ref="editInput"
                                @keydown.escape="cancelEdit()"
                                @keydown.enter="saveEdit(person)"
                                class="w-full border border-emerald-400 rounded-lg px-3 py-2 text-base focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-emerald-50"
                                maxlength="100"
                            >
                            <div class="flex items-center gap-2 mt-2">
                                <button
                                    type="button"
                                    @click="saveEdit(person)"
                                    class="text-emerald-600 hover:text-emerald-700 transition-colors p-1.5 rounded hover:bg-emerald-50"
                                    title="保存"
                                >
                                    <x-icon name="floppy-disk" class="w-5 h-5" />
                                </button>
                                <button
                                    type="button"
                                    @click="deleteSupportNetwork(person)"
                                    class="text-gray-500 hover:text-gray-700 transition-colors p-1.5 rounded hover:bg-gray-50"
                                    title="削除"
                                >
                                    <x-icon name="trash" class="w-5 h-5" />
                                </button>
                                <span
                                    @click="cancelEdit()"
                                    class="text-sm text-gray-500 hover:text-gray-700 underline cursor-pointer select-none ml-1"
                                >
                                    キャンセル
                                </span>
                            </div>
                        </div>

                        <!-- 表示モード（クリックで編集） -->
                        <div
                            x-show="editingId !== person.id"
                            @click="startEdit(person)"
                            class="cursor-pointer hover:bg-gray-50 rounded-lg p-1 -m-1 transition-colors"
                            title="クリックして編集"
                        >
                            <div class="flex items-center gap-3">
                                <!-- 人物アイコン -->
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <p class="text-gray-800 break-words overflow-wrap-anywhere font-medium" x-text="person.name"></p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </template>

        <!-- 空の状態 -->
        <div x-show="supportNetworks.length === 0" class="text-center py-12 text-gray-500">
            <div class="mb-4 flex justify-center text-gray-300"><x-icon name="user-group" class="w-12 h-12" /></div>
            <p>サポートネットワークがありません</p>
            <p class="text-sm mt-2">困った時に頼れる人を登録しましょう</p>
        </div>
    </div>
</div>

<script>
function supportNetworkApp() {
    return {
        supportNetworks: [],
        newSupportNetwork: {
            name: ''
        },
        editingId: null,
        editName: '',
        loading: false,
        error: '',

        async init() {
            await this.loadSupportNetworks();
        },

        async loadSupportNetworks() {
            const res = await apiFetch('/api/support-networks');
            this.supportNetworks = await res.json();
        },

        async createSupportNetwork() {
            this.error = '';

            if (!this.newSupportNetwork.name.trim()) {
                this.error = 'サポート者の名前を入力してください';
                return;
            }

            this.loading = true;
            try {
                const res = await apiFetch('/api/support-networks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.newSupportNetwork)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'エラーが発生しました');
                }

                this.newSupportNetwork = { name: '' };
                await this.loadSupportNetworks();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        startEdit(person) {
            this.editingId = person.id;
            this.editName = person.name;
            // 次のティックでインプットにフォーカス
            this.$nextTick(() => {
                const input = document.querySelector(`[x-ref="editInput"]`);
                if (input) {
                    input.focus();
                    input.select();
                }
            });
        },

        cancelEdit() {
            this.editingId = null;
            this.editName = '';
        },

        async saveEdit(person) {
            if (!this.editName.trim()) {
                alert('サポート者の名前を入力してください');
                return;
            }

            try {
                const res = await apiFetch(`/api/support-networks/${person.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.editName
                    })
                });

                if (res.ok) {
                    this.cancelEdit();
                    await this.loadSupportNetworks();
                }
            } catch (e) {
                console.error(e);
            }
        },

        async deleteSupportNetwork(person) {
            if (!confirm('このサポート者を削除しますか？')) return;

            try {
                await apiFetch(`/api/support-networks/${person.id}`, {
                    method: 'DELETE',
                });
                await this.loadSupportNetworks();
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection

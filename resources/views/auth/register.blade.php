@extends('layouts.app')

@section('title', '会員登録 - ココロの避難所')
@section('page-title', '会員登録')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-md mx-auto mt-4 sm:mt-10" x-data="registerApp()" x-cloak>

    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-200">
        <!-- アイコン -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto mb-4 text-green-600">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.5 11C10.7091 11 12.5 9.20914 12.5 7C12.5 4.79086 10.7091 3 8.5 3C6.29086 3 4.5 4.79086 4.5 7C4.5 9.20914 6.29086 11 8.5 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20 8V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 11H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">会員登録</h2>
            <p class="text-sm text-gray-500 mt-1">アカウントを作成してはじめましょう</p>
        </div>

        <form @submit.prevent="register()">
            <!-- 名前 -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">名前</label>
                <input
                    type="text"
                    x-model="form.name"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="お名前を入力"
                    required
                    autocomplete="name"
                >
                <template x-if="errors.name">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.name[0]"></p>
                </template>
            </div>

            <!-- メールアドレス -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">メールアドレス</label>
                <input
                    type="email"
                    x-model="form.email"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="example@mail.com"
                    required
                    autocomplete="email"
                >
                <template x-if="errors.email">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.email[0]"></p>
                </template>
            </div>

            <!-- パスワード -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">パスワード</label>
                <div class="relative">
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        x-model="form.password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="8文字以上で入力"
                        required
                        autocomplete="new-password"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">8文字以上で設定してください</p>
                <template x-if="errors.password">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.password[0]"></p>
                </template>
            </div>

            <!-- パスワード確認 -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">パスワード（確認）</label>
                <input
                    :type="showPassword ? 'text' : 'password'"
                    x-model="form.password_confirmation"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="もう一度パスワードを入力"
                    required
                    autocomplete="new-password"
                >
            </div>

            <!-- エラーメッセージ -->
            <div x-show="generalError" class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                <p class="text-red-600 text-sm" x-text="generalError"></p>
            </div>

            <!-- 登録ボタン -->
            <button
                type="submit"
                class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="loading"
            >
                <span x-show="!loading">会員登録</span>
                <span x-show="loading" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    登録中...
                </span>
            </button>
        </form>

        <!-- ログインリンク -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                すでにアカウントをお持ちの方は
                <a href="/login" class="text-emerald-600 font-semibold hover:text-emerald-700 hover:underline">ログイン</a>
            </p>
        </div>
    </div>
</div>

<script>
function registerApp() {
    return {
        form: {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        },
        errors: {},
        generalError: '',
        loading: false,
        showPassword: false,

        async register() {
            this.errors = {};
            this.generalError = '';
            this.loading = true;

            try {
                await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });

                const res = await fetch('/api/auth/register', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': decodeURIComponent(
                            document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || ''
                        ),
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await res.json();

                if (!res.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    this.generalError = data.message || '登録に失敗しました';
                    return;
                }

                window.location.href = '/';
            } catch (e) {
                this.generalError = '通信エラーが発生しました。もう一度お試しください。';
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endsection

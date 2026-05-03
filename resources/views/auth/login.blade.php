@extends('layouts.app')

@section('title', 'ログイン - ココケア')
@section('page-title', 'ログイン')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-md mx-auto mt-4 sm:mt-10" x-data="loginApp()" x-cloak>

    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-200">
        <!-- アイコン -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto mb-4 text-green-600">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M15 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 17L15 12L10 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">ログイン</h2>
            <p class="text-sm text-gray-500 mt-1">アカウント情報を入力してください</p>
        </div>

        <!-- メール認証完了メッセージ -->
        <template x-if="verified">
            <div class="mb-4 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-700 text-sm font-medium">メール認証が完了しました！ログインしてください。</p>
                </div>
            </div>
        </template>

        <form @submit.prevent="login()">
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
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">パスワード</label>
                <div class="relative">
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        x-model="form.password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="パスワードを入力"
                        required
                        autocomplete="current-password"
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
                <template x-if="errors.password">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.password[0]"></p>
                </template>
            </div>

            <!-- エラーメッセージ -->
            <div x-show="generalError && !emailNotVerified" class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                <p class="text-red-600 text-sm" x-text="generalError"></p>
            </div>

            <!-- メール未認証メッセージ -->
            <div x-show="emailNotVerified" class="mb-4 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                <div class="flex items-start gap-2 mb-3">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-amber-700" x-text="generalError"></p>
                </div>
                <button
                    type="button"
                    @click="resendVerification()"
                    class="w-full bg-amber-500 text-white py-2 px-4 rounded-lg text-sm font-semibold hover:bg-amber-600 transition-colors disabled:opacity-50"
                    :disabled="resending || resendCooldown > 0"
                >
                    <span x-show="!resending && resendCooldown <= 0">確認メールを再送する</span>
                    <span x-show="!resending && resendCooldown > 0" x-text="resendCooldown + '秒後に再送できます'"></span>
                    <span x-show="resending">送信中...</span>
                </button>
                <div x-show="resendSuccess" class="mt-2">
                    <p class="text-green-600 text-sm" x-text="resendSuccess"></p>
                </div>
            </div>

            <!-- ログインボタン -->
            <button
                type="submit"
                class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="loading"
            >
                <span x-show="!loading">ログイン</span>
                <span x-show="loading" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    ログイン中...
                </span>
            </button>
        </form>

        <!-- 会員登録リンク -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                アカウントをお持ちでない方は
                <a href="/register" class="text-emerald-600 font-semibold hover:text-emerald-700 hover:underline">会員登録</a>
            </p>
        </div>
    </div>
</div>

<script>
function loginApp() {
    return {
        form: {
            email: '',
            password: '',
        },
        errors: {},
        generalError: '',
        loading: false,
        showPassword: false,
        emailNotVerified: false,
        resending: false,
        resendCooldown: 0,
        resendSuccess: '',
        verified: new URLSearchParams(window.location.search).has('verified'),

        async login() {
            this.errors = {};
            this.generalError = '';
            this.emailNotVerified = false;
            this.resendSuccess = '';
            this.loading = true;

            try {
                await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });

                const res = await fetch('/api/auth/login', {
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
                    if (data.email_not_verified) {
                        this.emailNotVerified = true;
                        this.generalError = data.message;
                        return;
                    }
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    this.generalError = data.message || 'ログインに失敗しました';
                    return;
                }

                window.location.href = '/';
            } catch (e) {
                this.generalError = '通信エラーが発生しました。もう一度お試しください。';
            } finally {
                this.loading = false;
            }
        },

        async resendVerification() {
            this.resending = true;
            this.resendSuccess = '';

            try {
                await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });

                const res = await fetch('/api/auth/email/resend', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': decodeURIComponent(
                            document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || ''
                        ),
                    },
                    body: JSON.stringify({ email: this.form.email }),
                });

                const data = await res.json();
                if (res.ok) {
                    this.resendSuccess = data.message;
                    this.startResendCooldown();
                }
            } catch (e) {
                // エラー時は何もしない
            } finally {
                this.resending = false;
            }
        },

        startResendCooldown() {
            this.resendCooldown = 60;
            const timer = setInterval(() => {
                this.resendCooldown--;
                if (this.resendCooldown <= 0) {
                    clearInterval(timer);
                }
            }, 1000);
        },
    };
}
</script>
@endsection

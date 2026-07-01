@extends('layouts.app')

@section('title', 'ログイン - ' . config('app.name'))
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

    @unless (app()->environment('production'))
        <div class="mt-4 bg-amber-50 border border-amber-200 rounded-2xl p-4 sm:p-5">
            <div class="flex items-center gap-2 mb-3">
                <x-icon name="user-group" class="w-5 h-5 text-amber-600 flex-shrink-0" />
                <h3 class="text-sm font-semibold text-amber-900">テストアカウント（本番以外）</h3>
            </div>
            <p class="text-xs text-amber-800 mb-4">検証用のログイン情報です。ボタンを押すとフォームに自動入力されます。</p>

            <div class="space-y-3">
                @foreach (config('test_members.accounts', []) as $account)
                    <div class="bg-white border border-amber-100 rounded-xl p-3 sm:p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <p class="text-sm font-semibold text-gray-800">{{ $account['name'] }}</p>
                            <button
                                type="button"
                                @click="fillTestAccount(@js($account['email']), @js(config('test_members.password')))"
                                class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-2 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors"
                                title="フォームに入力"
                            >
                                <x-icon name="pencil-square" class="w-4 h-4" />
                                フォームに入力
                            </button>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 w-20 flex-shrink-0">メール</span>
                                <code class="flex-1 min-w-0 text-xs sm:text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 truncate">{{ $account['email'] }}</code>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 w-20 flex-shrink-0">PW</span>
                                <code class="flex-1 min-w-0 text-xs sm:text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">{{ config('test_members.password') }}</code>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endunless

    <!-- 入力成功トースト -->
    <div
        x-show="showFillToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2"
    >
        <x-icon name="pencil-square" class="w-5 h-5" />
        <span>フォームに入力しました！</span>
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
        showFillToast: false,

        fillTestAccount(email, password) {
            this.form.email = email;
            this.form.password = password;
            this.errors = {};
            this.generalError = '';
            this.emailNotVerified = false;
            this.resendSuccess = '';

            this.showFillToast = true;
            setTimeout(() => {
                this.showFillToast = false;
            }, 2000);
        },

        async login() {
            this.errors = {};
            this.generalError = '';
            this.emailNotVerified = false;
            this.resendSuccess = '';
            this.loading = true;

            try {
                const res = await apiFetch('/api/auth/login', {
                    method: 'POST',
                    refreshCsrfCookie: true,
                    headers: {
                        'Content-Type': 'application/json',
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

                window.location.href = '/mypage';
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
                const res = await apiFetch('/api/auth/email/resend', {
                    method: 'POST',
                    refreshCsrfCookie: true,
                    headers: {
                        'Content-Type': 'application/json',
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

@extends('layouts.app')

@section('title', 'メール確認 - ココケア')
@section('page-title', 'メール確認')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-md mx-auto mt-4 sm:mt-10" x-data="verifyEmailApp()" x-cloak>

    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-200">
        <!-- アイコン -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto mb-4 text-green-600">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">確認メールを送信しました</h2>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                ご登録いただいたメールアドレスに確認メールを送信しました。<br>
                メールに記載されたURLをクリックして、会員登録を完了してください。
            </p>
        </div>

        <!-- 注意書き -->
        <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-6">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-amber-700">
                    メールが届かない場合は、迷惑メールフォルダをご確認いただくか、下のボタンからメールを再送してください。
                </p>
            </div>
        </div>

        <!-- 成功メッセージ -->
        <div x-show="successMessage" class="mb-4 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
            <p class="text-green-600 text-sm" x-text="successMessage"></p>
        </div>

        <!-- エラーメッセージ -->
        <div x-show="errorMessage" class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
            <p class="text-red-600 text-sm" x-text="errorMessage"></p>
        </div>

        <!-- 再送ボタン -->
        <button
            @click="resend()"
            class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-emerald-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="loading || cooldown > 0"
        >
            <span x-show="!loading && cooldown <= 0">確認メールを再送する</span>
            <span x-show="!loading && cooldown > 0" x-text="cooldown + '秒後に再送できます'"></span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                送信中...
            </span>
        </button>

        <!-- ログインリンク -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                <a href="/login" class="text-emerald-600 font-semibold hover:text-emerald-700 hover:underline">ログイン画面に戻る</a>
            </p>
        </div>
    </div>
</div>

<script>
function verifyEmailApp() {
    return {
        loading: false,
        cooldown: 0,
        successMessage: '',
        errorMessage: '',

        async resend() {
            this.loading = true;
            this.successMessage = '';
            this.errorMessage = '';

            const params = new URLSearchParams(window.location.search);
            const email = params.get('email');

            if (!email) {
                this.errorMessage = 'メールアドレスが見つかりません。もう一度会員登録をお試しください。';
                this.loading = false;
                return;
            }

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
                    body: JSON.stringify({ email }),
                });

                const data = await res.json();

                if (res.ok) {
                    this.successMessage = data.message;
                    this.startCooldown();
                } else {
                    this.errorMessage = data.message || '送信に失敗しました。';
                }
            } catch (e) {
                this.errorMessage = '通信エラーが発生しました。もう一度お試しください。';
            } finally {
                this.loading = false;
            }
        },

        startCooldown() {
            this.cooldown = 60;
            const timer = setInterval(() => {
                this.cooldown--;
                if (this.cooldown <= 0) {
                    clearInterval(timer);
                }
            }, 1000);
        },
    };
}
</script>
@endsection

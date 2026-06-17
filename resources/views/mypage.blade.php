@extends('layouts.app')

@section('title', 'マイページ - ' . config('app.name'))
@section('page-title', 'マイページ')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="myPage()" x-init="init()">
    <!-- ウェルカムカード -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-emerald-100 p-6 sm:p-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-emerald-700 font-medium">おかえりなさい</p>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900" x-text="memberName ? memberName + 'さん' : 'マイページ'"></h1>
            </div>
        </div>
    </div>

    <!-- 今日やったこと -->
    <div class="bg-white rounded-2xl shadow-lg border border-emerald-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-400 to-teal-500 px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                今日やったこと
            </h2>
            <p class="text-emerald-50 text-sm mt-1" x-text="formattedDate"></p>
        </div>

        <div class="p-6">
            <div x-show="loading" class="flex justify-center py-8">
                <svg class="animate-spin h-8 w-8 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <template x-if="!loading && hasActivities">
                <ul class="space-y-3">
                    <template x-for="activity in activities" :key="activity.message">
                        <li class="flex items-start gap-3 bg-emerald-50 rounded-xl px-4 py-3 border border-emerald-100">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            <span class="text-gray-800 leading-relaxed" x-text="activity.message"></span>
                        </li>
                    </template>
                </ul>
            </template>

            <div x-show="!loading && !hasActivities" class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-gray-600 font-medium">今日はまだ記録がありません</p>
                <p class="text-gray-400 text-sm mt-1">ワークページからセルフケアを始めてみましょう</p>
            </div>
        </div>
    </div>

    <!-- ワークページへのリンク -->
    <a href="{{ route('home') }}" class="block group">
        <div class="bg-white rounded-2xl shadow-lg border border-emerald-100 p-6 sm:p-8 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] hover:border-emerald-300">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center flex-shrink-0 shadow-md group-hover:shadow-lg transition-shadow">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-emerald-700 transition-colors">ワークページへ</h2>
                        <p class="text-sm text-gray-500 mt-0.5">コラム法・問題解決法など各機能を使う</p>
                    </div>
                </div>
                <svg class="w-6 h-6 text-emerald-500 flex-shrink-0 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </div>
    </a>
</div>

<script>
function myPage() {
    return {
        loading: true,
        memberName: '',
        activities: [],
        hasActivities: false,
        formattedDate: '',

        async init() {
            this.formattedDate = new Date().toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                weekday: 'short',
            });

            try {
                const [meRes, activitiesRes] = await Promise.all([
                    apiFetch('/api/auth/me'),
                    apiFetch('/api/mypage/today-activities'),
                ]);

                if (meRes.ok) {
                    const meData = await meRes.json();
                    this.memberName = meData.member?.name ?? '';
                }

                if (activitiesRes.ok) {
                    const data = await activitiesRes.json();
                    this.activities = data.activities ?? [];
                    this.hasActivities = data.has_activities ?? false;
                }
            } catch (e) {
                // API失敗時は空状態のまま表示
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endsection

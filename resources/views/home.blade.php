@extends('layouts.app')

@section('title', config('app.name'))
@section('page-title', 'ワーク')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<!-- 振り返り放置通知バナー -->
<div x-data="overdueReflectionBanner()" x-init="init()" x-cloak>
    <div x-show="hasOverdue" class="max-w-4xl mx-auto mb-4">
        <div class="bg-yellow-50 border border-yellow-300 rounded-xl px-4 py-3 flex items-start gap-3">
            <x-icon name="exclamation-triangle" class="w-6 h-6 text-yellow-500 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-yellow-800">
                振り返りしてない実行計画があります。<a href="/problem-solvings/plans?filter=pending" class="text-yellow-900 font-bold underline hover:text-yellow-700">振り返り一覧</a>から確認できます。
            </p>
        </div>
    </div>
</div>

<div class="space-y-0">
    <x-work-section
        title="日々のセルフケア"
        description="気分の確認や、リラックス・つながりを整える"
    >
        <x-feature-card href="/condition-checks" title="コンディションチェック">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M8 14C8.5 12.5 10 11.5 12 11.5C14 11.5 15.5 12.5 16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="9" cy="10" r="1" fill="currentColor"/>
                    <circle cx="15" cy="10" r="1" fill="currentColor"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/self-compassion-journals" title="セルフコンパッション日記">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M21 8.25C21 5.76472 18.9013 3.75 16.3125 3.75C14.3769 3.75 12.7153 4.87628 12 6.48342C11.2847 4.87628 9.62312 3.75 7.6875 3.75C5.09867 3.75 3 5.76472 3 8.25C3 15.4706 12 20.25 12 20.25C12 20.25 21 15.4706 21 8.25Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/mindfulness" title="マインドフルネス瞑想">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 21C12 21 4 16 4 10C4 7 6 4 9 4C10.5 4 11.5 5 12 6C12.5 5 13.5 4 15 4C18 4 20 7 20 10C20 16 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="8" r="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 10V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M9 17C9 17 10.5 15 12 15C13.5 15 15 17 15 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/copings" title="コーピングリスト">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.27 2 8.5C2 5.41 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.08C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.41 22 8.5C22 12.27 18.6 15.36 13.45 20.03L12 21.35Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/support-networks" title="サポートネットワーク">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot>
        </x-feature-card>
    </x-work-section>

    <x-work-section
        title="認知行動療法（CBT）"
        description="思考や行動のパターンを見直す"
    >
        <x-feature-card href="/stressor-and-responses" title="ストレッサーとストレス反応">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/columns" title="認知再構成法(コラム法)">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 6V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="17" r="1" fill="currentColor"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/problem-solvings/list" title="問題解決法">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/exposures/list" title="エクスポージャー療法">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M8 21h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </x-slot>
        </x-feature-card>
    </x-work-section>

    <x-work-section
        title="スキーマ療法"
        description="自分の心のしくみを深く理解する"
    >
        <x-feature-card href="/schema-therapy/chronology" title="スキーマ年表">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 2V22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="6" r="2" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="18" r="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M14 6H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M4 12H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M14 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/early-maladaptive-schemas" title="早期不適応的スキーマ">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a2 2 0 012 2v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a2 2 0 01-2 2h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H6a2 2 0 01-2-2v-3a1 1 0 00-1-1H2a2 2 0 110-4h1a1 1 0 001-1V8a2 2 0 012-2h3a1 1 0 001-1V4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/schema-therapy/mode-work/dialogue" title="スキーマモードの対話ワーク">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 9H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M8 13H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </x-slot>
        </x-feature-card>
    </x-work-section>

    <x-work-section
        title="記録・その他"
        description="自由に記録したり、ストレスのパターンを整理する"
        :is-last="true"
    >
        <x-feature-card href="/stress-person-encyclopedias" title="ストレス人物図鑑">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot>
        </x-feature-card>

        <x-feature-card href="/simple-notepads/list" title="メモ帳">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.5 2.50001C18.8978 2.10219 19.4374 1.87869 20 1.87869C20.5626 1.87869 21.1022 2.10219 21.5 2.50001C21.8978 2.89784 22.1213 3.4374 22.1213 4.00001C22.1213 4.56262 21.8978 5.10219 21.5 5.50001L12 15L8 16L9 12L18.5 2.50001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot>
        </x-feature-card>
    </x-work-section>
</div>

<script>
function overdueReflectionBanner() {
    return {
        hasOverdue: false,
        async init() {
            try {
                const res = await apiFetch('/api/problem-solvings/has-overdue-reflection');
                if (!res.ok) return;
                const data = await res.json();
                this.hasOverdue = data.has_overdue;
            } catch (e) {
                // API失敗時は非表示のまま
            }
        }
    };
}
</script>
@endsection

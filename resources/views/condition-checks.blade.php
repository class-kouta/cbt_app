@extends('layouts.app')

@section('title', 'コンディションチェック')
@section('page-title', 'コンディションチェック')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-200">
        <div class="flex flex-col items-center text-center mb-8">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mb-4 text-green-600">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M8 14C8.5 12.5 10 11.5 12 11.5C14 11.5 15.5 12.5 16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="9" cy="10" r="1" fill="currentColor"/>
                    <circle cx="15" cy="10" r="1" fill="currentColor"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                現在のコンディションを記録しましょう
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a
                href="/condition-checks/create"
                class="flex flex-col items-center justify-center gap-3 p-6 rounded-xl border-2 border-emerald-200 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 hover:border-emerald-300 transition-all"
            >
                <x-icon name="document-text" class="w-10 h-10" />
                <span class="font-semibold text-lg">新規記録</span>
            </a>
            <a
                href="/condition-checks/list"
                class="flex flex-col items-center justify-center gap-3 p-6 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-800 hover:bg-gray-100 hover:border-gray-300 transition-all"
            >
                <x-icon name="clipboard-document" class="w-10 h-10 text-emerald-600" />
                <span class="font-semibold text-lg">記録一覧</span>
            </a>
        </div>
    </div>
</div>
@endsection

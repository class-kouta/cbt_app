@extends('layouts.app')

@section('title', 'マインドフルネス瞑想 - ココロの避難所')
@section('page-title', 'マインドフルネス瞑想')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 md:p-10 border border-gray-200">
        <div class="flex flex-col items-center text-center mb-8">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mb-4 text-green-600">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 21C12 21 4 16 4 10C4 7 6 4 9 4C10.5 4 11.5 5 12 6C12.5 5 13.5 4 15 4C18 4 20 7 20 10C20 16 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="8" r="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 10V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M9 17C9 17 10.5 15 12 15C13.5 15 15 17 15 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">マインドフルネス瞑想</h1>
            <p class="text-gray-500 text-sm sm:text-base">このページは現在準備中です</p>
        </div>

        <div class="bg-green-50 rounded-xl p-6 sm:p-8 text-center">
            <div class="text-4xl mb-4">🧘</div>
            <h2 class="text-lg sm:text-xl font-semibold text-green-800 mb-3">Coming Soon...</h2>
            <p class="text-green-700 text-sm sm:text-base leading-relaxed">
                マインドフルネス瞑想の機能は現在開発中です。<br>
                今しばらくお待ちください。
            </p>
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="inline-flex items-center gap-2 text-green-600 hover:text-green-800 font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                トップに戻る
            </a>
        </div>
    </div>
</div>
@endsection

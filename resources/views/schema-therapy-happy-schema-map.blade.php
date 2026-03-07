@extends('layouts.app')

@section('title', 'ハッピースキーママップ - ココロの避難所')
@section('page-title', 'ハッピースキーママップ')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-6 sm:p-8 md:p-10 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <a href="/schema-therapy" class="inline-flex items-center gap-1 text-green-600 hover:text-green-700 font-medium transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                戻る
            </a>
        </div>

        <div class="text-center py-16">
            <div class="w-20 h-20 mx-auto mb-6 text-emerald-400">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M9 11C9 11 10 12.5 12 12.5C14 12.5 15 11 15 11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="9" cy="8" r="1" fill="currentColor"/>
                    <circle cx="15" cy="8" r="1" fill="currentColor"/>
                    <path d="M3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M3 12C3 16.9706 7.02944 21 12 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="2 3"/>
                    <path d="M12 21V17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M8 21H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3">準備中です</h2>
            <p class="text-gray-500 text-sm sm:text-base">この機能は現在開発中です。もう少々お待ちください。</p>
        </div>
    </div>
</div>
@endsection

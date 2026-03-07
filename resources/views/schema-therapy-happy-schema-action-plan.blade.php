@extends('layouts.app')

@section('title', 'ハッピースキーマと行動計画 - ココロの避難所')
@section('page-title', 'ハッピースキーマと行動計画')

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
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 14L10 16L14 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3">準備中です</h2>
            <p class="text-gray-500 text-sm sm:text-base">この機能は現在開発中です。もう少々お待ちください。</p>
        </div>
    </div>
</div>
@endsection

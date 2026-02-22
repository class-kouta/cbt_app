@extends('layouts.app')

@section('title', '安全なイメージと安全な何か - ココロの避難所')
@section('page-title', '安全なイメージと安全な何か')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-6 sm:p-8 md:p-10 border border-gray-200">
        <div class="flex flex-col items-center text-center py-12">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mb-6 text-green-600">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M12 22C12 22 20 18 20 12V5L12 2L4 5V12C4 18 12 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-4">安全なイメージと安全な何か</h1>
            <p class="text-gray-500 text-base sm:text-lg">このページは現在準備中です。</p>
            <p class="text-gray-400 text-sm mt-2">近日公開予定</p>
            <a href="/schema-therapy" class="mt-8 inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                スキーマ療法に戻る
            </a>
        </div>
    </div>
</div>
@endsection

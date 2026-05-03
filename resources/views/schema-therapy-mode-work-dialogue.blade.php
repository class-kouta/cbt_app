@extends('layouts.app')

@section('title', '対話のワーク - ' . config('app.name'))
@section('page-title', '対話のワーク')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-6 sm:p-8 md:p-10 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <a href="/schema-therapy/mode-work" class="inline-flex items-center gap-1 text-green-600 hover:text-green-700 font-medium transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                戻る
            </a>
        </div>

        <div class="text-center py-16">
            <div class="w-20 h-20 mx-auto mb-6 text-purple-400">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 9H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M8 13H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3">準備中です</h2>
            <p class="text-gray-500 text-sm sm:text-base">この機能は現在開発中です。もう少々お待ちください。</p>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'スキーマ療法 - ココロの避難所')
@section('page-title', 'スキーマ療法')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<div class="max-w-4xl mx-auto grid grid-cols-2 gap-3 sm:gap-4 md:gap-6">
    <!-- 安全なイメージと安全な何か Card -->
    <a href="/schema-therapy/safe-image" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M12 22C12 22 20 18 20 12V5L12 2L4 5V12C4 18 12 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">安全なイメージと安全な何か</h2>
            </div>
        </div>
    </a>

    <!-- 年表 Card -->
    <a href="/schema-therapy/chronology" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M12 2V22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="12" cy="6" r="2" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="18" r="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M14 6H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M4 12H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M14 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">年表</h2>
            </div>
        </div>
    </a>

    <!-- 早期不適応的スキーマ Card -->
    <a href="/early-maladaptive-schemas" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a2 2 0 012 2v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a2 2 0 01-2 2h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H6a2 2 0 01-2-2v-3a1 1 0 00-1-1H2a2 2 0 110-4h1a1 1 0 001-1V8a2 2 0 012-2h3a1 1 0 001-1V4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">早期不適応的スキーマ</h2>
            </div>
        </div>
    </a>

    <!-- スキーマカウント Card -->
    <a href="/early-maladaptive-schemas/count" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M16 4H18C18.5304 4 19.0391 4.21071 19.4142 4.58579C19.7893 4.96086 20 5.46957 20 6V20C20 20.5304 19.7893 21.0391 19.4142 21.4142C19.0391 21.7893 18.5304 22 18 22H6C5.46957 22 4.96086 21.7893 4.58579 21.4142C4.21071 21.0391 4 20.5304 4 20V6C4 5.46957 4.21071 4.96086 4.58579 4.58579C4.96086 4.21071 5.46957 4 6 4H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 2H9C8.44772 2 8 2.44772 8 3V5C8 5.55228 8.44772 6 9 6H15C15.5523 6 16 5.55228 16 5V3C16 2.44772 15.5523 2 15 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 14L11 16L15 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">スキーマカウント</h2>
            </div>
        </div>
    </a>
</div>
@endsection

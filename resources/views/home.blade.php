@extends('layouts.app')

@section('title', 'ココロの避難所')
@section('page-title', 'トップ')

@section('body-class', 'bg-gradient-to-br from-emerald-50 to-teal-50')

@section('content')
<!-- Cards Grid - Always 2 columns for mobile friendliness -->
<div class="max-w-4xl mx-auto grid grid-cols-2 gap-3 sm:gap-4 md:gap-6">
    <!-- Stressor and Stress Response Card -->
    <a href="/stressor-and-responses" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <!-- Stressor Icon -->
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">ストレッサーとストレス反応</h2>
            </div>
        </div>
    </a>

    <!-- Column Method (CBT) Card -->
    <a href="/columns" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <!-- CBT Icon -->
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 6V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="12" cy="17" r="1" fill="currentColor"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">コラム法（CBT）</h2>
            </div>
        </div>
    </a>

    <!-- Coping List Card -->
    <a href="/copings" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <!-- Coping List Icon -->
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.27 2 8.5C2 5.41 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.08C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.41 22 8.5C22 12.27 18.6 15.36 13.45 20.03L12 21.35Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">コーピングリスト</h2>
            </div>
        </div>
    </a>

    <!-- Writing Disclosure Card -->
    <a href="/writing-disclosures" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <!-- Writing Disclosure Icon -->
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 9H9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">筆記開示</h2>
            </div>
        </div>
    </a>

    <!-- Problem Solving Card -->
    <a href="/problem-solvings" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <!-- Problem Solving Icon -->
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">問題解決法</h2>
            </div>
        </div>
    </a>

    <!-- Simple Notepad Card -->
    <a href="/simple-notepads" class="block group">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
            <div class="flex flex-col items-center text-center">
                <!-- Simple Notepad Icon -->
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.5 2.50001C18.8978 2.10219 19.4374 1.87869 20 1.87869C20.5626 1.87869 21.1022 2.10219 21.5 2.50001C21.8978 2.89784 22.1213 3.4374 22.1213 4.00001C22.1213 4.56262 21.8978 5.10219 21.5 5.50001L12 15L8 16L9 12L18.5 2.50001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">メモ帳</h2>
            </div>
        </div>
    </a>
</div>
@endsection

@extends('layouts.app')

@section('title', '管理画面 - メニュー')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">🔧 管理画面</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- コーピングリスト管理メニュー -->
            <a href="/siteAdmPanel63/coping/menu" class="block group">
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200 hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 mb-4 text-purple-600">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.27 2 8.5C2 5.41 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.08C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.41 22 8.5C22 12.27 18.6 15.36 13.45 20.03L12 21.35Z" fill="currentColor"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">コーピングリスト管理</h2>
                        <p class="text-sm text-gray-600">コーピングリストに関する設定を管理</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="text-indigo-600 hover:text-indigo-800 text-sm">← トップに戻る</a>
        </div>
    </div>
</div>
@endsection

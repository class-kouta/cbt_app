@extends('layouts.app')

@section('title', 'コーピングリスト管理メニュー')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">💜 コーピングリスト管理</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- コーピングリストタグ管理 -->
            <a href="/siteAdmPanel63/coping/tag" class="block group">
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200 hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 mb-4 text-purple-600">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                <path d="M20.59 13.41L13.42 20.58C13.2343 20.766 13.0137 20.9135 12.7709 21.0141C12.5281 21.1148 12.2678 21.1666 12.005 21.1666C11.7422 21.1666 11.4819 21.1148 11.2391 21.0141C10.9963 20.9135 10.7757 20.766 10.59 20.58L2 12V2H12L20.59 10.59C20.9625 10.9647 21.1716 11.4716 21.1716 12C21.1716 12.5284 20.9625 13.0353 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 7H7.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">コーピングリストタグ管理</h2>
                        <p class="text-sm text-gray-600">コーピングリストに使用するタグを管理</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="mt-8 text-center">
            <a href="/siteAdmPanel63/menu" class="text-indigo-600 hover:text-indigo-800 text-sm">← 管理メニューに戻る</a>
        </div>
    </div>
</div>
@endsection

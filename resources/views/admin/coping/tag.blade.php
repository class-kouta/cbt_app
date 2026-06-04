@extends('layouts.app')

@section('title', 'コーピングリストタグ管理')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center"><x-icon name="tag" class="w-8 h-8 inline-block" /> コーピングリストタグ管理</h1>
        
        <div class="bg-gray-100 rounded-xl p-12 text-center">
            <div class="mb-4 flex justify-center text-gray-300"><x-icon name="wrench-screwdriver" class="w-16 h-16" /></div>
            <p class="text-xl text-gray-600 font-medium">これから作成</p>
            <p class="text-sm text-gray-500 mt-2">このページは現在準備中です</p>
        </div>

        <div class="mt-8 text-center">
            <a href="/siteAdmPanel63/coping/menu" class="text-indigo-600 hover:text-indigo-800 text-sm">← コーピングリスト管理メニューに戻る</a>
        </div>
    </div>
</div>
@endsection

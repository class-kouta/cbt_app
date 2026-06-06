@extends('layouts.app')

@section('title', '管理画面 - メニュー')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center"><x-icon name="wrench-screwdriver" class="w-8 h-8 inline-block" /> 管理画面</h1>

        <div class="text-center text-gray-500 py-8">
            <p>現在、管理できる項目はありません</p>
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="text-indigo-600 hover:text-indigo-800 text-sm">← トップに戻る</a>
        </div>
    </div>
</div>
@endsection

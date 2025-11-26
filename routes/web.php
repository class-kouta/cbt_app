<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// TODOリストページ
Route::get('/todos', function () {
    return view('todos');
});

// 完了済みTODO一覧ページ
Route::get('/todos/completed', function () {
    return view('completed-todos');
});

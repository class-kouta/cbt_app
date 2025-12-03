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

// コーピングリストページ
Route::get('/copings', function () {
    return view('copings');
});

// コラム法ページ
Route::get('/columns', function () {
    return view('columns');
});

// コラム一覧ページ
Route::get('/columns/list', function () {
    return view('columns-list');
});

// コラム詳細ページ
Route::get('/columns/{id}', function ($id) {
    return view('column-detail', ['columnId' => $id]);
})->where('id', '[0-9]+');

// クイックタスク管理ページ
Route::get('/quick-tasks', function () {
    return view('quick-tasks');
});

// 筆記開示ページ
Route::get('/writing-disclosures', function () {
    return view('writing-disclosures');
});

// 筆記開示一覧ページ
Route::get('/writing-disclosures/list', function () {
    return view('writing-disclosures-list');
});

// 筆記開示詳細ページ
Route::get('/writing-disclosures/{id}', function ($id) {
    return view('writing-disclosure-detail', ['itemId' => $id]);
})->where('id', '[0-9]+');

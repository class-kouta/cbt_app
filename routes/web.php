<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
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

// 問題解決法作成ページ
Route::get('/problem-solvings', function () {
    return view('problem-solving-edit');
});

// 問題解決法一覧ページ
Route::get('/problem-solvings/list', function () {
    return view('problem-solvings-list');
});

// 問題解決法詳細ページ
Route::get('/problem-solvings/{id}', function ($id) {
    return view('problem-solving-detail', ['itemId' => $id]);
})->where('id', '[0-9]+');

// 問題解決法編集ページ
Route::get('/problem-solvings/{id}/edit', function ($id) {
    return view('problem-solving-edit', ['itemId' => $id]);
})->where('id', '[0-9]+');

// ===========================================
// 管理画面ルート
// ===========================================

// 管理メニュー
Route::get('/siteAdmPanel63/menu', function () {
    return view('admin.menu');
});

// コーピングリスト管理メニュー
Route::get('/siteAdmPanel63/coping/menu', function () {
    return view('admin.coping.menu');
});

// コーピングリストタグ管理
Route::get('/siteAdmPanel63/coping/tag', function () {
    return view('admin.coping.tag');
});

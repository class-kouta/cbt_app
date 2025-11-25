<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// TODOリストページ
Route::get('/todos', function () {
    return view('todos');
});

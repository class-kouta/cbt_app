<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-todo-create', function () {
    return view('test-todo-create');
});

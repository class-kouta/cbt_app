<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\DifficultyController;

// Todo API
Route::get('/todos', [TodoController::class, 'index']);
Route::get('/todos/completed', [TodoController::class, 'completed']);
Route::post('/todos', [TodoController::class, 'store']);
Route::patch('/todos/{todo}/complete', [TodoController::class, 'complete']);
Route::delete('/todos/{todo}', [TodoController::class, 'destroy']);

// Tag API（一覧取得のみ）
Route::get('/tags', [TagController::class, 'index']);

// Difficulty API（一覧取得のみ）
Route::get('/difficulties', [DifficultyController::class, 'index']);


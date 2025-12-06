<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\DifficultyController;
use App\Http\Controllers\CopingController;
use App\Http\Controllers\CopingTagController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\QuickTaskController;
use App\Http\Controllers\WritingDisclosureController;
use App\Http\Controllers\ProblemSolvingController;

// Todo API
Route::get('/todos', [TodoController::class, 'index']);
Route::get('/todos/completed', [TodoController::class, 'completed']);
Route::post('/todos', [TodoController::class, 'store']);
Route::patch('/todos/{todo}/complete', [TodoController::class, 'complete']);
Route::patch('/todos/{todo}/uncomplete', [TodoController::class, 'uncomplete']);
Route::delete('/todos/{todo}', [TodoController::class, 'destroy']);

// Tag API
Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/with-trashed', [TagController::class, 'indexWithTrashed']);
Route::post('/tags', [TagController::class, 'store']);
Route::put('/tags/{id}', [TagController::class, 'update']);
Route::delete('/tags/{id}', [TagController::class, 'destroy']);
Route::post('/tags/{id}/restore', [TagController::class, 'restore']);

// Difficulty API（一覧取得のみ）
Route::get('/difficulties', [DifficultyController::class, 'index']);

// Coping API
Route::get('/copings', [CopingController::class, 'index']);
Route::post('/copings', [CopingController::class, 'store']);
Route::put('/copings/{coping}', [CopingController::class, 'update']);
Route::delete('/copings/{coping}', [CopingController::class, 'destroy']);

// CopingTag API（一覧取得のみ）
Route::get('/coping-tags', [CopingTagController::class, 'index']);

// Column API（コラム法）
Route::get('/columns', [ColumnController::class, 'index']);
Route::get('/columns/{column}', [ColumnController::class, 'show']);
Route::post('/columns', [ColumnController::class, 'store']);
Route::put('/columns/{column}', [ColumnController::class, 'update']);
Route::delete('/columns/{column}', [ColumnController::class, 'destroy']);

// QuickTask API（クイックタスク）
Route::get('/quick-tasks', [QuickTaskController::class, 'index']);
Route::post('/quick-tasks', [QuickTaskController::class, 'store']);
Route::put('/quick-tasks/{quickTask}', [QuickTaskController::class, 'update']);
Route::delete('/quick-tasks/{quickTask}', [QuickTaskController::class, 'destroy']);

// WritingDisclosure API（筆記開示）
Route::get('/writing-disclosures', [WritingDisclosureController::class, 'index']);
Route::post('/writing-disclosures', [WritingDisclosureController::class, 'store']);
Route::put('/writing-disclosures/{writingDisclosure}', [WritingDisclosureController::class, 'update']);
Route::delete('/writing-disclosures/{writingDisclosure}', [WritingDisclosureController::class, 'destroy']);

// ProblemSolving API（問題解決法）
Route::get('/problem-solvings', [ProblemSolvingController::class, 'index']);
Route::get('/problem-solvings/{problemSolving}', [ProblemSolvingController::class, 'show']);
Route::post('/problem-solvings', [ProblemSolvingController::class, 'store']);
Route::put('/problem-solvings/{problemSolving}', [ProblemSolvingController::class, 'update']);
Route::delete('/problem-solvings/{problemSolving}', [ProblemSolvingController::class, 'destroy']);
// 解決策
Route::post('/problem-solvings/{problemSolving}/solutions', [ProblemSolvingController::class, 'addSolution']);
Route::put('/problem-solvings/{problemSolving}/solutions/{solution}', [ProblemSolvingController::class, 'updateSolution']);
Route::delete('/problem-solvings/{problemSolving}/solutions/{solution}', [ProblemSolvingController::class, 'deleteSolution']);

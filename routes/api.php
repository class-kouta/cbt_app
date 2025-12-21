<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CopingController;
use App\Http\Controllers\CopingTagController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\WritingDisclosureController;
use App\Http\Controllers\ProblemSolvingController;

// Coping API
Route::get('/copings', [CopingController::class, 'index']);
Route::post('/copings', [CopingController::class, 'store']);
Route::put('/copings/{coping}', [CopingController::class, 'update']);
Route::delete('/copings/{coping}', [CopingController::class, 'destroy']);
Route::patch('/copings/reorder', [CopingController::class, 'reorder']);

// CopingTag API（一覧取得のみ）
Route::get('/coping-tags', [CopingTagController::class, 'index']);

// Column API（コラム法）
Route::get('/columns', [ColumnController::class, 'index']);
Route::get('/columns/{column}', [ColumnController::class, 'show']);
Route::post('/columns', [ColumnController::class, 'store']);
Route::put('/columns/{column}', [ColumnController::class, 'update']);
Route::delete('/columns/{column}', [ColumnController::class, 'destroy']);

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

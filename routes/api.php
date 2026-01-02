<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CopingController;
use App\Http\Controllers\CopingTagController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\WritingDisclosureController;
use App\Http\Controllers\ProblemSolvingController;
use App\Http\Controllers\SimpleNotepadController;
use App\Http\Controllers\StressorAndResponseController;
use App\Http\Controllers\SupportNetworkController;
use App\Http\Controllers\EarlyMaladaptiveSchemaController;

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

// SimpleNotepad API（シンプルメモ帳）
Route::get('/simple-notepads', [SimpleNotepadController::class, 'index']);
Route::post('/simple-notepads', [SimpleNotepadController::class, 'store']);
Route::put('/simple-notepads/{simpleNotepad}', [SimpleNotepadController::class, 'update']);
Route::delete('/simple-notepads/{simpleNotepad}', [SimpleNotepadController::class, 'destroy']);

// StressorAndResponse API（ストレッサーとストレス反応）
Route::get('/stressor-and-responses', [StressorAndResponseController::class, 'index']);
Route::get('/stressor-and-responses/{stressorAndResponse}', [StressorAndResponseController::class, 'show']);
Route::post('/stressor-and-responses', [StressorAndResponseController::class, 'store']);
Route::put('/stressor-and-responses/{stressorAndResponse}', [StressorAndResponseController::class, 'update']);
Route::delete('/stressor-and-responses/{stressorAndResponse}', [StressorAndResponseController::class, 'destroy']);

// SupportNetwork API（サポートネットワーク）
Route::get('/support-networks', [SupportNetworkController::class, 'index']);
Route::post('/support-networks', [SupportNetworkController::class, 'store']);
Route::put('/support-networks/{supportNetwork}', [SupportNetworkController::class, 'update']);
Route::delete('/support-networks/{supportNetwork}', [SupportNetworkController::class, 'destroy']);

// EarlyMaladaptiveSchema API（スキーマ療法 - 早期不適応スキーマ）
Route::get('/early-maladaptive-schemas', [EarlyMaladaptiveSchemaController::class, 'show']);
Route::post('/early-maladaptive-schemas', [EarlyMaladaptiveSchemaController::class, 'store']);
Route::put('/early-maladaptive-schemas/{earlyMaladaptiveSchema}', [EarlyMaladaptiveSchemaController::class, 'update']);

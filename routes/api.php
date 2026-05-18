<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChronologyController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\CopingController;
use App\Http\Controllers\CopingTagController;
use App\Http\Controllers\EarlyMaladaptiveSchemaController;
use App\Http\Controllers\HappySchemaActionPlanController;
use App\Http\Controllers\ModeMapController;
use App\Http\Controllers\ProblemSolvingController;
use App\Http\Controllers\SafePlaceController;
use App\Http\Controllers\SimpleNotepadController;
use App\Http\Controllers\StressorAndResponseController;
use App\Http\Controllers\SupportNetworkController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\SchemaModeMonitoringController;
use App\Http\Controllers\DialogueWorkController;
use App\Http\Controllers\HealthyAdultModeImageController;
use App\Http\Controllers\ModeDialogueWorkController;
use App\Http\Controllers\MindfulnessController;
use App\Http\Controllers\WritingDisclosureController;
use Illuminate\Support\Facades\Route;

// Member Auth API（会員認証）
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
// Coping API
Route::get('/copings', [CopingController::class, 'index']);
Route::post('/copings', [CopingController::class, 'store']);
Route::put('/copings/{coping}', [CopingController::class, 'update']);
Route::delete('/copings/{coping}', [CopingController::class, 'destroy']);

// CopingTag API（一覧取得のみ）
Route::get('/coping-tags', [CopingTagController::class, 'index']);

// Tag API（汎用タグ一覧取得）
Route::get('/tags', [TagController::class, 'index']);

// Column API（コラム法）
Route::get('/columns', [ColumnController::class, 'index']);
Route::get('/columns/adaptive-thoughts', [ColumnController::class, 'adaptiveThoughts']);
Route::get('/columns/export/csv', [ColumnController::class, 'exportCsv']);
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
Route::get('/problem-solvings/export/csv', [ProblemSolvingController::class, 'exportCsv']);
Route::get('/problem-solvings/plans', [ProblemSolvingController::class, 'plans']);
Route::get('/problem-solvings/has-overdue-reflection', [ProblemSolvingController::class, 'hasOverdueReflection'])->name('api.problem-solvings.has-overdue-reflection');
Route::get('/problem-solvings/{problemSolving}', [ProblemSolvingController::class, 'show']);
Route::post('/problem-solvings', [ProblemSolvingController::class, 'store']);
Route::put('/problem-solvings/{problemSolving}', [ProblemSolvingController::class, 'update']);
Route::delete('/problem-solvings/{problemSolving}', [ProblemSolvingController::class, 'destroy']);
// 解決策
Route::post('/problem-solvings/{problemSolving}/solutions', [ProblemSolvingController::class, 'addSolution']);
Route::put('/problem-solvings/{problemSolving}/solutions/{solution}', [ProblemSolvingController::class, 'updateSolution']);
Route::delete('/problem-solvings/{problemSolving}/solutions/{solution}', [ProblemSolvingController::class, 'deleteSolution']);
// 計画
Route::post('/problem-solvings/{problemSolving}/plans', [ProblemSolvingController::class, 'addPlan']);
Route::put('/problem-solvings/{problemSolving}/plans/{plan}', [ProblemSolvingController::class, 'updatePlan']);
Route::delete('/problem-solvings/{problemSolving}/plans/{plan}', [ProblemSolvingController::class, 'deletePlan']);

// SimpleNotepad API（シンプルメモ帳）
Route::get('/simple-notepads', [SimpleNotepadController::class, 'index']);
Route::post('/simple-notepads', [SimpleNotepadController::class, 'store']);
Route::put('/simple-notepads/{simpleNotepad}', [SimpleNotepadController::class, 'update']);
Route::delete('/simple-notepads/{simpleNotepad}', [SimpleNotepadController::class, 'destroy']);

// StressorAndResponse API（ストレッサーとストレス反応）
Route::get('/stressor-and-responses', [StressorAndResponseController::class, 'index']);
Route::get('/stressor-and-responses/export/csv', [StressorAndResponseController::class, 'exportCsv']);
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

// SafePlace API（スキーマ療法 - 安全なイメージと安全な何か）
Route::get('/safe-places', [SafePlaceController::class, 'show']);
Route::post('/safe-places', [SafePlaceController::class, 'store']);
Route::put('/safe-places/{id}', [SafePlaceController::class, 'update']);

// ModeMap API（スキーマ療法 - モードマップ簡易版）
Route::get('/mode-maps', [ModeMapController::class, 'show']);
Route::get('/mode-maps/export/csv', [ModeMapController::class, 'exportCsv']);
Route::post('/mode-maps', [ModeMapController::class, 'store']);
Route::put('/mode-maps/{id}', [ModeMapController::class, 'update']);

// HappySchemaActionPlan API（スキーマ療法 - ハッピースキーマと行動計画）
Route::get('/happy-schema-action-plans', [HappySchemaActionPlanController::class, 'show']);
Route::get('/happy-schema-action-plans/export/csv', [HappySchemaActionPlanController::class, 'exportCsv']);
Route::post('/happy-schema-action-plans', [HappySchemaActionPlanController::class, 'store']);
Route::put('/happy-schema-action-plans/{id}', [HappySchemaActionPlanController::class, 'update']);

// SchemaModeMonitoring API（スキーマ療法 - セルフモニタリング）
Route::get('/schema-mode-monitorings', [SchemaModeMonitoringController::class, 'index']);
Route::get('/schema-mode-monitorings/{schemaModeMonitoring}', [SchemaModeMonitoringController::class, 'show']);
Route::post('/schema-mode-monitorings', [SchemaModeMonitoringController::class, 'store']);
Route::put('/schema-mode-monitorings/{schemaModeMonitoring}', [SchemaModeMonitoringController::class, 'update']);
Route::delete('/schema-mode-monitorings/{schemaModeMonitoring}', [SchemaModeMonitoringController::class, 'destroy']);

// DialogueWork API（スキーマ療法 - ヘルシーサイドとスキーマサイドの対話のワーク）
Route::get('/dialogue-works', [DialogueWorkController::class, 'index']);
Route::get('/dialogue-works/{dialogueWork}', [DialogueWorkController::class, 'show']);
Route::post('/dialogue-works', [DialogueWorkController::class, 'store']);
Route::put('/dialogue-works/{dialogueWork}', [DialogueWorkController::class, 'update']);
Route::delete('/dialogue-works/{dialogueWork}', [DialogueWorkController::class, 'destroy']);

// ModeDialogueWork API（スキーマ療法 - モードワーク対話のワーク）
Route::get('/mode-dialogue-works', [ModeDialogueWorkController::class, 'index']);
Route::get('/mode-dialogue-works/{modeDialogueWork}', [ModeDialogueWorkController::class, 'show']);
Route::post('/mode-dialogue-works', [ModeDialogueWorkController::class, 'store']);
Route::put('/mode-dialogue-works/{modeDialogueWork}', [ModeDialogueWorkController::class, 'update']);
Route::delete('/mode-dialogue-works/{modeDialogueWork}', [ModeDialogueWorkController::class, 'destroy']);

// HealthyAdultModeImage API（スキーマ療法 - ヘルシーな大人モードのイメージ）
Route::get('/healthy-adult-mode-images', [HealthyAdultModeImageController::class, 'show']);
Route::post('/healthy-adult-mode-images', [HealthyAdultModeImageController::class, 'store']);
Route::put('/healthy-adult-mode-images/{id}', [HealthyAdultModeImageController::class, 'update']);

// Mindfulness API（マインドフルネス瞑想 - 音声URL取得）
Route::get('/mindfulness/audio-url', [MindfulnessController::class, 'getAudioUrl']);

// Chronology API（スキーマ療法 - 年表）
Route::get('/chronologies', [ChronologyController::class, 'index']);
Route::get('/chronologies/export/csv', [ChronologyController::class, 'exportCsv']);
Route::get('/chronologies/{chronology}', [ChronologyController::class, 'show']);
Route::post('/chronologies', [ChronologyController::class, 'store']);
Route::put('/chronologies/{chronology}', [ChronologyController::class, 'update']);
Route::delete('/chronologies/{chronology}', [ChronologyController::class, 'destroy']);
});

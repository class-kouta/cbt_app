<?php

use Illuminate\Support\Facades\Route;

// 会員認証ページ（未ログインのみ）
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', function () {
        return request()->user()->hasVerifiedEmail()
            ? redirect()->route('home')
            : view('auth.verify-email');
    })->name('verification.notice');

    Route::middleware('verified')->group(function () {
        Route::get('/mypage', function () {
            return view('mypage');
        })->name('mypage');

        Route::get('/', function () {
            return view('home');
        })->name('home');

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

        // 適応的思考一覧ページ
        Route::get('/columns/adaptive-thoughts', function () {
            return view('column-adaptive-thoughts-list');
        });

        // コラム詳細ページ
        Route::get('/columns/{id}', function ($id) {
            return view('column-detail', ['columnId' => $id]);
        })->where('id', '[0-9]+');

        // コラム編集ページ
        Route::get('/columns/{id}/edit', function ($id) {
            return view('columns', ['columnId' => $id]);
        })->where('id', '[0-9]+');

        // クイックタスク管理ページ
        Route::get('/quick-tasks', function () {
            return view('quick-tasks');
        });

        // コンディションチェック
        Route::get('/condition-checks', function () {
            return view('condition-checks-list');
        });

        Route::redirect('/condition-checks/list', '/condition-checks');

        Route::get('/condition-checks/create', function () {
            return view('condition-checks-form');
        });

        Route::get('/condition-checks/{id}/edit', function ($id) {
            return view('condition-checks-form', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        // セルフコンパッション日記
        Route::get('/self-compassion-journals', function () {
            return view('self-compassion-journals');
        });

        Route::get('/self-compassion-journals/list', function () {
            return view('self-compassion-journals-list');
        });

        Route::get('/self-compassion-journals/{id}', function ($id) {
            return view('self-compassion-journal-detail', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        Route::get('/self-compassion-journals/{id}/edit', function ($id) {
            return view('self-compassion-journals', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        // ストレス人物図鑑
        Route::get('/stress-person-encyclopedias', function () {
            return view('stress-person-encyclopedias');
        });

        Route::get('/stress-person-encyclopedias/list', function () {
            return view('stress-person-encyclopedias-list');
        });

        Route::get('/stress-person-encyclopedias/{id}', function ($id) {
            return view('stress-person-encyclopedia-detail', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        Route::get('/stress-person-encyclopedias/{id}/edit', function ($id) {
            return view('stress-person-encyclopedias', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        // シンプルメモ帳新規作成ページ
        Route::get('/simple-notepads', function () {
            return view('simple-notepads');
        });

        // シンプルメモ帳一覧ページ
        Route::get('/simple-notepads/list', function () {
            return view('simple-notepads-list');
        });

        // シンプルメモ帳編集ページ
        Route::get('/simple-notepads/{id}/edit', function ($id) {
            return view('simple-notepads', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        // メモ帳タグ管理ページ
        Route::get('/simple-notepad-tags', function () {
            return view('simple-notepad-tags');
        });

        // エクスポージャー療法作成ページ
        Route::get('/exposures', function () {
            return view('exposure-edit');
        });

        // エクスポージャー療法一覧ページ
        Route::get('/exposures/list', function () {
            return view('exposures-list');
        });

        // 実施記録一覧ページ
        Route::get('/exposures/sessions/new', function () {
            return view('exposure-session-edit');
        });

        Route::get('/exposures/sessions/{id}', function ($id) {
            return view('exposure-session-edit', ['sessionId' => $id]);
        })->where('id', '[0-9]+');

        Route::get('/exposures/sessions', function () {
            return view('exposure-sessions-list');
        });

        // エクスポージャー療法詳細・編集ページ（統合）
        Route::get('/exposures/{id}', function ($id) {
            return view('exposure-edit', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        Route::get('/exposures/{id}/edit', function ($id) {
            return redirect('/exposures/' . $id);
        })->where('id', '[0-9]+');

        // 問題解決法作成ページ
        Route::get('/problem-solvings', function () {
            return view('problem-solving-edit');
        });

        // 問題解決法一覧ページ
        Route::get('/problem-solvings/list', function () {
            return view('problem-solvings-list');
        });

        // 振り返り一覧ページ
        Route::get('/problem-solvings/plans', function () {
            return view('problem-solving-plans-list');
        })->name('problem-solving-plans.list');

        // 振り返りページ
        Route::get('/problem-solvings/plans/new', function () {
            return view('problem-solving-plan-edit');
        });

        // 振り返り編集ページ
        Route::get('/problem-solvings/plans/{id}', function ($id) {
            return view('problem-solving-plan-edit', ['planId' => $id]);
        })->where('id', '[0-9]+');

        // 問題解決法詳細・編集ページ（統合）
        Route::get('/problem-solvings/{id}', function ($id) {
            return view('problem-solving-edit', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        // 旧編集URLからのリダイレクト
        Route::get('/problem-solvings/{id}/edit', function ($id) {
            return redirect('/problem-solvings/' . $id);
        })->where('id', '[0-9]+');

        // ストレッサーとストレス反応作成ページ
        Route::get('/stressor-and-responses', function () {
            return view('stressor-and-responses');
        });

        // ストレッサーとストレス反応一覧ページ
        Route::get('/stressor-and-responses/list', function () {
            return view('stressor-and-responses-list');
        });

        // ストレッサーとストレス反応詳細ページ
        Route::get('/stressor-and-responses/{id}', function ($id) {
            return view('stressor-and-response-detail', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        // ストレッサーとストレス反応編集ページ
        Route::get('/stressor-and-responses/{id}/edit', function ($id) {
            return view('stressor-and-responses', ['itemId' => $id]);
        })->where('id', '[0-9]+');

        // サポートネットワークページ
        Route::get('/support-networks', function () {
            return view('support-networks');
        });

        // マインドフルネス瞑想ページ
        Route::get('/mindfulness', function () {
            return view('mindfulness', [
                'sounds' => \App\Enums\MindfulnessSound::toFrontendArray(),
                'durations' => \App\Enums\MindfulnessDuration::values(),
            ]);
        });

        // スキーマ療法ページ
        Route::prefix('schema-therapy')->name('schema-therapy.')->group(function () {
            Route::redirect('/', '/')->name('index');

            Route::get('/chronology', function () {
                return view('schema-therapy-chronology');
            })->name('chronology');

            Route::get('/chronology/create', function () {
                return view('schema-therapy-chronology-edit');
            })->name('chronology.create');

            Route::get('/chronology/{id}/edit', function ($id) {
                return view('schema-therapy-chronology-edit', ['itemId' => $id]);
            })->where('id', '[0-9]+')->name('chronology.edit');

            // モードワーク（旧ハブ → 対話ワークへリダイレクト）
            Route::redirect('/mode-work', '/schema-therapy/mode-work/dialogue')->name('mode-work');

            // モードワーク - 対話のワーク一覧
            Route::get('/mode-work/dialogue', function () {
                return view('schema-therapy-mode-work-dialogue-list');
            })->name('mode-work.dialogue');

            // モードワーク - 対話のワーク作成
            Route::get('/mode-work/dialogue/create', function () {
                return view('schema-therapy-mode-work-dialogue-edit');
            })->name('mode-work.dialogue.create');

            // モードワーク - 対話のワーク編集
            Route::get('/mode-work/dialogue/{id}/edit', function ($id) {
                return view('schema-therapy-mode-work-dialogue-edit', ['itemId' => $id]);
            })->where('id', '[0-9]+')->name('mode-work.dialogue.edit');

        });

        // 早期不適応スキーマページ
        Route::prefix('early-maladaptive-schemas')->name('early-maladaptive-schemas.')->group(function () {
            Route::get('/', function () {
                return view('early-maladaptive-schemas');
            })->name('index');
        });
    });
});

// ===========================================
// 管理画面ルート
// ===========================================

// 管理メニュー
Route::get('/siteAdmPanel63/menu', function () {
    return view('admin.menu');
});


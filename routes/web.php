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

        // 筆記開示編集ページ
        Route::get('/writing-disclosures/{id}/edit', function ($id) {
            return view('writing-disclosures', ['itemId' => $id]);
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

        // 問題解決法作成ページ
        Route::get('/problem-solvings', function () {
            return view('problem-solving-edit');
        });

        // 問題解決法一覧ページ
        Route::get('/problem-solvings/list', function () {
            return view('problem-solvings-list');
        });

        // 計画一覧ページ
        Route::get('/problem-solvings/plans', function () {
            return view('problem-solving-plans-list');
        })->name('problem-solving-plans.list');

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
            Route::get('/', function () {
                return redirect('/');
            })->name('index');

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
            Route::get('/mode-work', function () {
                return redirect('/schema-therapy/mode-work/dialogue');
            })->name('mode-work');

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

// コーピングリスト管理メニュー
Route::get('/siteAdmPanel63/coping/menu', function () {
    return view('admin.coping.menu');
});

// コーピングリストタグ管理
Route::get('/siteAdmPanel63/coping/tag', function () {
    return view('admin.coping.tag');
});

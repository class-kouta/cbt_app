# マインドフルネス機能（自然音再生）実装計画書

## 概要

マインドフルネスの実践をサポートする「自然音再生」機能を追加します。
ページを開き、3種類の自然音と5段階の再生時間を選択して再生できるシンプルな音声プレイヤーです。

音声ファイルはHerokuアドオン **Bucketeer（AWS S3）** に配置し、ブラウザから直接S3にアクセスして再生します。

---

## 機能の目的

- マインドフルネス瞑想の実践をサポートする
- 自然音を聴くことでリラクゼーションを促す
- 時間を区切って集中できる環境を提供する

---

## 機能要件

### 音の種類（3種類）

| ID | 名称 | 説明 |
|----|------|------|
| rain | 雨音 | 雨が降る音 |
| forest | 森の音 | 鳥のさえずりや風の音 |
| ocean | 波の音 | 海の波の音 |

### 再生時間（5段階）

| 時間（分） |
|-----------|
| 5 |
| 10 |
| 15 |
| 20 |
| 25 |

### 操作

- 音の種類を選択する
- 再生時間を選択する
- 再生 / 一時停止ができる
- 残り時間がプログレスバーと数字で表示される
- 再生終了時に自動で停止する

---

## 音声ファイル仕様

### ファイル形式

- 元ファイル: `.wav`
- 配信形式: **`.mp3`（推奨）** または `.wav`

`.mp3` を推奨する理由:

| 形式 | 25分のファイルサイズ目安 |
|------|----------------------|
| .wav（44.1kHz, 16bit, ステレオ） | 約250MB |
| .mp3（192kbps） | 約35MB |

15ファイル合計で `.wav` だと数GB、`.mp3` なら数百MBに収まる。
S3の転送料金・ブラウザの読み込み速度の観点から `.mp3` が望ましい。

### 変換コマンド（ffmpeg）

```bash
ffmpeg -i rain_5.wav -codec:audio libmp3lame -qscale:a 2 rain_5.mp3
```

### ファイル命名規則

```
{音の種類ID}_{再生時間（分）}.mp3
```

### ファイル一覧（計15ファイル）

```
rain_5.mp3
rain_10.mp3
rain_15.mp3
rain_20.mp3
rain_25.mp3
forest_5.mp3
forest_10.mp3
forest_15.mp3
forest_20.mp3
forest_25.mp3
ocean_5.mp3
ocean_10.mp3
ocean_15.mp3
ocean_20.mp3
ocean_25.mp3
```

---

## インフラ構成

### Bucketeer（Herokuアドオン）

Bucketeerは、HerokuからAWS S3バケットを簡単に利用できるアドオン。
プロビジョニング時に以下の環境変数が自動的にHerokuに設定される。

| 環境変数 | 説明 |
|---------|------|
| `BUCKETEER_AWS_ACCESS_KEY_ID` | AWSアクセスキー |
| `BUCKETEER_AWS_SECRET_ACCESS_KEY` | AWSシークレットキー |
| `BUCKETEER_AWS_REGION` | AWSリージョン |
| `BUCKETEER_BUCKET_NAME` | S3バケット名 |

### S3バケット内のディレクトリ構成

```
<BUCKETEER_BUCKET_NAME>/
└── audio/
    └── mindfulness/
        ├── rain_5.mp3
        ├── rain_10.mp3
        ├── ...
        └── ocean_25.mp3
```

### アーキテクチャ図

```
[ブラウザ]
    │
    │ 1. ページ表示リクエスト
    ▼
[Heroku (Laravel)]
    │
    │ 2. Blade テンプレート返却
    ▼
[ブラウザ]
    │
    │ 3. 音声選択時に API コール
    ▼
[Heroku (Laravel)]  ── 4. S3 の音声 URL を返却 ──►  [ブラウザ]
                                                        │
                                                        │ 5. Audio API で S3 から直接音声を取得・再生
                                                        ▼
                                                   [Bucketeer (S3)]
```

音声データ自体はS3からブラウザに直接配信されるため、Herokuサーバーに負荷がかからない。

---

## 実装詳細

### 1. Heroku 設定

#### Bucketeerアドオンの追加

```bash
heroku addons:create bucketeer:hobbyist --app <your-app-name>
```

#### 環境変数の確認

```bash
heroku config | grep BUCKETEER
```

#### S3への音声ファイルアップロード

```bash
AWS_ACCESS_KEY_ID=<BUCKETEER_AWS_ACCESS_KEY_ID> \
AWS_SECRET_ACCESS_KEY=<BUCKETEER_AWS_SECRET_ACCESS_KEY> \
aws s3 sync ./audio/mindfulness s3://<BUCKETEER_BUCKET_NAME>/audio/mindfulness \
    --content-type "audio/mpeg" \
    --acl public-read
```

#### Review Apps対応（app.json）

`app.json` の `addons` に Bucketeer を追加する。

```json
{
  "addons": [
    { "plan": "heroku-postgresql:essential-0" },
    { "plan": "bucketeer:hobbyist" }
  ]
}
```

### 2. Laravel 設定

#### composer パッケージ追加

```bash
composer require league/flysystem-aws-s3-v3
```

#### config/filesystems.php の S3 ディスク設定変更

Bucketeerの環境変数を優先し、ローカル開発用のAWS環境変数にフォールバックする。

```php
's3' => [
    'driver' => 's3',
    'key' => env('BUCKETEER_AWS_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID')),
    'secret' => env('BUCKETEER_AWS_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY')),
    'region' => env('BUCKETEER_AWS_REGION', env('AWS_DEFAULT_REGION', 'us-east-1')),
    'bucket' => env('BUCKETEER_BUCKET_NAME', env('AWS_BUCKET')),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'throw' => false,
    'report' => false,
],
```

#### config/services.php にマインドフルネス設定を追加

```php
'mindfulness' => [
    'audio_base_url' => env('MINDFULNESS_AUDIO_BASE_URL'),
],
```

#### .env.example に追加

```env
# マインドフルネス音声配信URL（S3バケットのURL or ローカルパス）
MINDFULNESS_AUDIO_BASE_URL=
```

### 3. バックエンド実装

#### ルーティング

```php
// routes/web.php
Route::get('/mindfulness', function () {
    return view('mindfulness');
});
```

```php
// routes/api.php
use App\Http\Controllers\MindfulnessController;

Route::get('/mindfulness/audio-url', [MindfulnessController::class, 'getAudioUrl']);
```

#### コントローラ

```php
// app/Http/Controllers/MindfulnessController.php

<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MindfulnessController extends Controller
{
    private const VALID_SOUNDS = ['rain', 'forest', 'ocean'];
    private const VALID_DURATIONS = [5, 10, 15, 20, 25];

    public function getAudioUrl(Request $request): JsonResponse
    {
        $sound = $request->input('sound');
        $duration = (int) $request->input('duration');

        if (!in_array($sound, self::VALID_SOUNDS) || !in_array($duration, self::VALID_DURATIONS)) {
            return response()->json(['error' => '無効なパラメータです'], 400);
        }

        $path = "audio/mindfulness/{$sound}_{$duration}.mp3";

        $baseUrl = config('services.mindfulness.audio_base_url');
        if ($baseUrl) {
            $url = rtrim($baseUrl, '/') . '/' . $path;
        } else {
            $url = Storage::disk('s3')->url($path);
        }

        return response()->json(['url' => $url]);
    }
}
```

`MINDFULNESS_AUDIO_BASE_URL` が設定されていればそのURLを使い、未設定なら `Storage::disk('s3')->url()` で生成する。

### 4. フロントエンド実装

#### 技術構成

| 技術 | 役割 |
|------|------|
| Blade | ページテンプレート（`layouts.app` を継承） |
| Alpine.js | 状態管理・音声制御 |
| Tailwind CSS | スタイリング |
| HTML5 Audio API | 音声再生（`new Audio()`） |

#### Blade テンプレート

`resources/views/mindfulness.blade.php` を新規作成する。

ページ構成:

1. **音の種類セクション** — 3種類をカード形式で並べ、選択状態をハイライト
2. **再生時間セクション** — 5つのボタンを横並びに配置
3. **プレイヤーセクション** — 再生/一時停止ボタン、プログレスバー、残り時間表示

#### Alpine.js コンポーネント

```javascript
function mindfulnessPlayer() {
    return {
        sounds: [
            { id: 'rain', name: '雨音', icon: '🌧️' },
            { id: 'forest', name: '森の音', icon: '🌲' },
            { id: 'ocean', name: '波の音', icon: '🌊' },
        ],
        durations: [5, 10, 15, 20, 25],
        selectedSound: null,
        selectedDuration: null,
        isPlaying: false,
        isLoading: false,
        currentTime: 0,
        totalDuration: 0,
        audio: null,

        selectSound(soundId) {
            this.selectedSound = soundId;
            this.stop();
        },

        selectDuration(minutes) {
            this.selectedDuration = minutes;
            this.stop();
        },

        async play() {
            if (!this.selectedSound || !this.selectedDuration) return;
            this.stop();
            this.isLoading = true;

            try {
                const res = await fetch(
                    `/api/mindfulness/audio-url?sound=${this.selectedSound}&duration=${this.selectedDuration}`
                );
                if (!res.ok) throw new Error('URL取得失敗');
                const data = await res.json();

                this.audio = new Audio(data.url);
                this.totalDuration = this.selectedDuration * 60;

                this.audio.addEventListener('timeupdate', () => {
                    this.currentTime = this.audio.currentTime;
                });
                this.audio.addEventListener('ended', () => {
                    this.isPlaying = false;
                    this.currentTime = 0;
                });
                this.audio.addEventListener('canplaythrough', () => {
                    this.isLoading = false;
                });

                await this.audio.play();
                this.isPlaying = true;
            } catch (e) {
                this.isLoading = false;
            }
        },

        pause() {
            if (this.audio) {
                this.audio.pause();
                this.isPlaying = false;
            }
        },

        stop() {
            if (this.audio) {
                this.audio.pause();
                this.audio.currentTime = 0;
                this.audio = null;
            }
            this.isPlaying = false;
            this.isLoading = false;
            this.currentTime = 0;
        },

        togglePlay() {
            if (this.isPlaying) {
                this.pause();
            } else if (this.audio && this.audio.paused) {
                this.audio.play();
                this.isPlaying = true;
            } else {
                this.play();
            }
        },

        get progress() {
            if (!this.totalDuration) return 0;
            return (this.currentTime / this.totalDuration) * 100;
        },

        get remainingTime() {
            const remaining = Math.max(0, this.totalDuration - this.currentTime);
            const min = Math.floor(remaining / 60);
            const sec = Math.floor(remaining % 60);
            return `${min}:${sec.toString().padStart(2, '0')}`;
        },

        get canPlay() {
            return this.selectedSound && this.selectedDuration;
        },
    };
}
```

### 5. ナビゲーション追加

`resources/views/layouts/app.blade.php` のハンバーガーメニューに「マインドフルネス」リンクを追加する。

`resources/views/home.blade.php` のトップ画面カードグリッドに「マインドフルネス」カードを追加する。

---

## ローカル開発環境での動作確認

ローカル開発ではS3を使わず、`public/` ディレクトリに音声ファイルを配置してテストできる。

### 手順

1. `public/audio/mindfulness/` ディレクトリを作成
2. テスト用の音声ファイルを配置
3. `.env` に以下を設定

```env
MINDFULNESS_AUDIO_BASE_URL=http://localhost:8081
```

コントローラが `MINDFULNESS_AUDIO_BASE_URL` を参照し、ローカルの音声ファイルを返す。

### .gitignore に追加

音声ファイルはリポジトリに含めない。

```
public/audio/
```

---

## ファイル構成（新規作成・変更予定）

### 新規作成

```
app/Http/Controllers/
  └── MindfulnessController.php

resources/views/
  └── mindfulness.blade.php
```

### 変更

```
config/filesystems.php          ... S3ディスク設定をBucketeer対応に変更
config/services.php             ... mindfulness設定を追加
routes/web.php                  ... /mindfulness ルート追加
routes/api.php                  ... /mindfulness/audio-url ルート追加
resources/views/layouts/app.blade.php ... ナビゲーションにリンク追加
resources/views/home.blade.php  ... トップ画面にカード追加
.env.example                    ... MINDFULNESS_AUDIO_BASE_URL 追加
.gitignore                      ... public/audio/ 追加
app.json                        ... Bucketeerアドオン追加
```

---

## やらないこと

- **ユーザーごとの再生履歴の保存**: DB設計は不要（静的ファイル再生のみ）
- **音声ファイルのアップロード機能**: 音声は事前にS3に配置する
- **バックグラウンド再生制御**: ブラウザ標準の挙動に委ねる
- **音量調整UI**: ブラウザ / 端末のボリュームに委ねる

---

## コスト見積もり

| 項目 | 月額目安 |
|------|---------|
| Bucketeer (hobbyist) | ~$5 |
| S3ストレージ（数百MB程度） | ~$0.01 |
| S3データ転送（少量想定） | ~$1以下 |
| **合計** | **~$5〜6** |

---

## 実装フェーズ

### Phase 1: インフラ準備

1. Bucketeerアドオンの追加
2. `config/filesystems.php` のS3設定変更
3. `config/services.php` にマインドフルネス設定追加
4. `.env.example` に環境変数追加
5. `composer require league/flysystem-aws-s3-v3`
6. 音声ファイルを `.mp3` に変換
7. S3に音声ファイルをアップロード

### Phase 2: バックエンド実装

1. `MindfulnessController` の作成
2. `routes/web.php` にルート追加
3. `routes/api.php` にAPI追加

### Phase 3: フロントエンド実装

1. `mindfulness.blade.php` の作成（Alpine.js プレイヤー）
2. ナビゲーションメニューにリンク追加
3. トップ画面にカード追加

### Phase 4: テスト・デプロイ

1. ローカル環境でのテスト
2. Review Appsでのテスト（`app.json` 更新）
3. 本番デプロイ

---

## 関連ドキュメント

- [DEVELOPMENT_PLAN.md](./DEVELOPMENT_PLAN.md) - 開発計画
- [HEROKU_PIPELINE_AND_REVIEW_APPS_SETUP.md](./HEROKU_PIPELINE_AND_REVIEW_APPS_SETUP.md) - Herokuパイプライン設定
- [DIRECTORY_STRUCTURE.md](./DIRECTORY_STRUCTURE.md) - ディレクトリ構成

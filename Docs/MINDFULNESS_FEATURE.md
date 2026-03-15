# マインドフルネス機能（自然音再生）— Cloudflare R2 セットアップ手順

## 概要

マインドフルネスの実践をサポートする「自然音再生」機能で使う音声ファイルを **Cloudflare R2** に配置し、ブラウザから再生できるようにするための手順書です。

Cloudflare R2 は AWS S3 互換の API を持つオブジェクトストレージで、**エグレス（データ転送）料金が無料**という大きな利点があります。Laravel からは S3 ドライバをそのまま利用できます。

---

## 音声ファイル仕様

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

### ファイル形式

- 元ファイル: `.wav`
- 配信形式: **`.mp3`（推奨）** または `.wav`

`.mp3` を推奨する理由:

| 形式 | 25分のファイルサイズ目安 |
|------|----------------------|
| .wav（44.1kHz, 16bit, ステレオ） | 約250MB |
| .mp3（192kbps） | 約35MB |

15ファイル合計で `.wav` だと数GB、`.mp3` なら数百MBに収まる。
R2の保存料金・ブラウザの読み込み速度の観点から `.mp3` が望ましい。

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

## アーキテクチャ

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
[Heroku (Laravel)]  ── 4. R2 の音声 URL を返却 ──►  [ブラウザ]
                                                        │
                                                        │ 5. Audio API で R2 から直接音声を取得・再生
                                                        ▼
                                                   [Cloudflare R2]
```

音声データ自体はR2からブラウザに直接配信されるため、Herokuサーバーに負荷がかからない。

---

## 手順 1: Cloudflare R2 の準備

### 1-1. バケットの作成

1. [Cloudflareダッシュボード](https://dash.cloudflare.com/) にログインし、左側メニューの「R2 オブジェクトストレージ」を選択
2. 「バケットを作成」をクリックし、バケット名を入力（例: `cbt-app-audio`）して作成
3. ロケーションは「アジア太平洋（APAC）」を選択すると日本からのアクセスが速い

### 1-2. 音声ファイルのアップロード

作成したバケットの詳細画面を開き、以下の手順でアップロードします。

1. バケット内で「フォルダを作成」をクリックし、`audio` フォルダを作成
2. `audio` フォルダに移動し、さらに `mindfulness` フォルダを作成
3. `audio/mindfulness/` フォルダ内に、15個の音声ファイル（`.mp3`）をドラッグ＆ドロップでアップロード

アップロード後のバケット内構成:

```
<バケット名>/
└── audio/
    └── mindfulness/
        ├── rain_5.mp3
        ├── rain_10.mp3
        ├── ...
        └── ocean_25.mp3
```

### 1-3. パブリックアクセスの設定（R2.dev サブドメイン）

音声ファイルをブラウザから直接再生できるよう、パブリックアクセスを有効にします。

1. バケットの「設定」タブを開く
2. 「パブリックアクセス」セクションの「R2.dev サブドメイン」で「アクセスを許可する」をクリック
3. 表示されたパブリックURL（例: `https://pub-xxxxxxxxxxxxxxxx.r2.dev`）をメモする

> **注意**: パブリックアクセスを有効にすると、バケット内の全ファイルが公開されます。音声ファイル以外のファイルは配置しないでください。

> **補足**: カスタムドメイン（例: `audio.your-domain.com`）を設定することもできます。Cloudflareで管理しているドメインがあれば、「カスタムドメイン」セクションから追加できます。

### 1-4. APIトークンの発行（任意）

アプリから `Storage::disk('s3')` を使ってR2のファイル一覧取得や署名付きURL生成をしたい場合に必要です。パブリックアクセスのみで運用する場合は不要です。

1. R2 ダッシュボードの右側にある「R2 API トークンの管理」→「APIトークンを作成する」をクリック
2. 権限は **「オブジェクト読み取りのみ」** で十分（アプリからアップロードはしないため）
3. 特定バケットに制限したい場合は「バケットを指定」でバケットを選択
4. 作成後に表示される以下の情報をメモする:
   - **アクセスキー ID**
   - **シークレットアクセスキー**
   - **S3 クライアント用のエンドポイント**（`https://<アカウントID>.r2.cloudflarestorage.com`）

---

## 手順 2: Laravel 側のセットアップ

### 2-1. S3 互換パッケージのインストール

Laravel から R2（S3互換）にアクセスするためのパッケージをインストールします。

```bash
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

### 2-2. `config/filesystems.php` の設定確認

S3 ディスク設定はデフォルトのまま利用可能ですが、**`throw` と `report` を `true` に変更**してください。`false` のままだとS3操作の失敗が無視され、音声が再生されない原因の特定が困難になります。

```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'throw' => true,
    'report' => true,
],
```

> **重要**: `FILESYSTEM_DISK` 環境変数は `local` のままにしてください。アプリ全体のデフォルトストレージを S3 に変更すると、他の機能に影響が出ます。音声URLの取得時にのみ `Storage::disk('s3')` で明示的にS3ディスクを使用します。

### 2-3. `config/services.php` にマインドフルネス設定を追加

```php
'mindfulness' => [
    'audio_base_url' => env('MINDFULNESS_AUDIO_BASE_URL'),
],
```

`MINDFULNESS_AUDIO_BASE_URL` が設定されている場合はその URL をベースとして音声ファイルの URL を組み立て、未設定の場合は `Storage::disk('s3')->url()` で生成します。ローカル開発環境でも R2 から直接音声を配信するため、通常は空のままで OK です。

### 2-4. `.env.example` に環境変数を追加

既存の `AWS_*` 環境変数に加えて以下を追加します。

```env
# Cloudflare R2 設定
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=cbt-app-audio
# ↓ Cloudflareダッシュボード > R2 > 概要 に表示される「S3 API」のエンドポイントURL
AWS_ENDPOINT=https://<YOUR_CLOUDFLARE_ACCOUNT_ID>.r2.cloudflarestorage.com
# ↓ Cloudflareダッシュボード > R2 > バケット > 設定 > パブリックアクセスで発行されるURL
AWS_URL=https://<YOUR_R2_PUBLIC_SUBDOMAIN>.r2.dev
AWS_USE_PATH_STYLE_ENDPOINT=true

# マインドフルネス音声配信URL（通常は空でR2から直接配信。ローカルファイル使用時のみ設定）
MINDFULNESS_AUDIO_BASE_URL=
```

**各環境変数の説明:**

| 環境変数 | 説明 | 取得場所 |
|---------|------|---------|
| `AWS_ACCESS_KEY_ID` | R2 APIトークンのアクセスキーID | R2ダッシュボード > 「R2 APIトークンの管理」 > トークン作成後に表示 |
| `AWS_SECRET_ACCESS_KEY` | R2 APIトークンのシークレットアクセスキー | 同上（作成時に一度だけ表示） |
| `AWS_DEFAULT_REGION` | R2では `auto` を指定 | 固定値 |
| `AWS_BUCKET` | R2バケット名 | R2ダッシュボード > バケット一覧 |
| `AWS_ENDPOINT` | R2のS3互換エンドポイント | R2ダッシュボード > 概要 > 「S3 API」欄 |
| `AWS_URL` | R2パブリックアクセスURL（R2.devサブドメイン） | R2ダッシュボード > バケット > 設定 > パブリックアクセス |
| `AWS_USE_PATH_STYLE_ENDPOINT` | R2では `true` を指定 | 固定値 |
| `MINDFULNESS_AUDIO_BASE_URL` | 通常は空（R2から直接配信）。ローカルファイルを使う場合のみ `http://localhost:8081` を指定 |

### 2-5. `.gitignore` に追加（任意）

ローカルファイルで動作確認する場合のみ、テスト用の音声ファイルをリポジトリに含めないよう追加します。R2 から直接配信する場合は不要です。

```
public/audio/
```

---

## 手順 3: アプリケーションの実装

### 3-1. ルーティング

ページ表示用のルート（`routes/web.php`）は既存のものをそのまま使用します。

```php
// routes/web.php（既存）
Route::get('/mindfulness', function () {
    return view('mindfulness');
});
```

音声URL取得用の API ルートを追加します。

```php
// routes/api.php に追加
use App\Http\Controllers\MindfulnessController;

Route::get('/mindfulness/audio-url', [MindfulnessController::class, 'getAudioUrl']);
```

### 3-2. Form Request（バリデーション）

```php
// app/Http/Requests/Mindfulness/GetAudioUrlRequest.php

<?php

namespace App\Http\Requests\Mindfulness;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAudioUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sound' => ['required', 'string', Rule::in(['rain', 'forest', 'ocean'])],
            'duration' => ['required', 'integer', Rule::in([5, 10, 15, 20, 25])],
        ];
    }

    public function messages(): array
    {
        return [
            'sound.required' => '音の種類を選択してください',
            'sound.in' => '無効な音の種類です',
            'duration.required' => '再生時間を選択してください',
            'duration.in' => '無効な再生時間です',
        ];
    }
}
```

### 3-3. コントローラ

```php
// app/Http/Controllers/MindfulnessController.php

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mindfulness\GetAudioUrlRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MindfulnessController extends Controller
{
    public function getAudioUrl(GetAudioUrlRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $sound = $validated['sound'];
        $duration = $validated['duration'];

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

**ポイント:**
- `MINDFULNESS_AUDIO_BASE_URL` が設定されていればそのURLを使い、未設定なら `Storage::disk('s3')->url()` で生成
- `Storage::disk('s3')` で明示的にS3ディスクを指定（デフォルトディスクは `local` のまま）
- バケット全体をスキャンするのではなく、音の種類と再生時間から直接ファイルパスを組み立てる
- バリデーションエラー時はLaravelが自動的に 422 レスポンスを返す

### 3-4. フロントエンド

既存の `resources/views/mindfulness.blade.php` にAlpine.jsで音声プレイヤーを実装します。APIから音声URLを取得して再生する方式です。

```javascript
// Alpine.js コンポーネントの音声再生部分
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
        // ... イベントリスナーの設定、再生処理
    } catch (e) {
        this.isLoading = false;
    }
}
```

---

## 手順 4: Heroku へのデプロイ

### 4-1. Heroku に環境変数をセットする

手順1でメモした Cloudflare R2 の情報を Heroku に設定します。

```bash
heroku config:set AWS_ACCESS_KEY_ID=<R2アクセスキーID> --app <your-app-name>
heroku config:set AWS_SECRET_ACCESS_KEY=<R2シークレットアクセスキー> --app <your-app-name>
heroku config:set AWS_DEFAULT_REGION=auto --app <your-app-name>
heroku config:set AWS_BUCKET=<R2バケット名> --app <your-app-name>
heroku config:set AWS_ENDPOINT=https://<アカウントID>.r2.cloudflarestorage.com --app <your-app-name>
heroku config:set AWS_URL=https://pub-xxxxxxxxxxxxxxxx.r2.dev --app <your-app-name>
heroku config:set AWS_USE_PATH_STYLE_ENDPOINT=true --app <your-app-name>
```

> **注意**: `FILESYSTEM_DISK` は設定しないでください（デフォルトの `local` のまま運用）。

### 4-2. デプロイを実行

```bash
git add .
git commit -m "Add Cloudflare R2 audio integration for mindfulness feature"
git push heroku main
```

---

## ローカル開発環境での動作確認

ローカル開発でも本番同様に **Cloudflare R2 から直接音声を配信** します。`.env` の R2 関連の環境変数（`AWS_*`）を正しく設定するだけで、本番と同じ音声ファイルを再生できます。

### 手順

1. `.env` に R2 の接続情報を設定（`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` 等）

2. `MINDFULNESS_AUDIO_BASE_URL` は **空のまま** にする

```env
MINDFULNESS_AUDIO_BASE_URL=
```

`MINDFULNESS_AUDIO_BASE_URL` が空の場合、コントローラは `Storage::disk('s3')->url()` で R2 パブリック URL を生成します。
例: `https://pub-xxxxxxxxxxxxxxxx.r2.dev/audio/mindfulness/rain_5.mp3`

> **メリット**: ローカルに音声ファイルを配置する手間がなく、本番と完全に同じ音声・URL構成で動作確認できます。

### （任意）ローカルファイルで確認したい場合

R2 にアクセスできない環境や、オフラインで開発したい場合は、従来どおりローカルファイルを使うこともできます。

1. `public/audio/mindfulness/` ディレクトリを作成

```bash
mkdir -p public/audio/mindfulness
```

2. テスト用の音声ファイルを配置（実際のmp3ファイル、またはダミーのmp3ファイル）

3. `.env` に以下を設定

```env
MINDFULNESS_AUDIO_BASE_URL=http://localhost:8081
```

コントローラが `MINDFULNESS_AUDIO_BASE_URL` を参照し、ローカルの音声ファイルURLを返します。
例: `http://localhost:8081/audio/mindfulness/rain_5.mp3`

4. `.gitignore` に `public/audio/` を追加してリポジトリに含めないようにする

```
public/audio/
```

---

## 署名付きURL（temporaryUrl）を使う場合

パブリックアクセスの代わりに、アクセスのたびに有効期限付きの一時URLを発行する方式も選択できます。ファイルURLの直リンク防止が必要な場合に有効です。

### 手順

1. R2 のパブリックアクセスは**無効のまま**にする（手順 1-3 をスキップ）
2. `AWS_URL` 環境変数は**設定しない**
3. APIトークン（手順 1-4）は**必須**
4. コントローラのURL生成部分を以下に変更:

```php
$url = Storage::disk('s3')->temporaryUrl($path, now()->addHour());
```

**トレードオフ:**

| 方式 | メリット | デメリット |
|------|---------|-----------|
| パブリックアクセス（推奨） | 高速、Cloudflareキャッシュ活用、APIトークン不要 | URLが固定で共有可能 |
| 署名付きURL | URLの有効期限制御、直リンク防止 | 毎回URL生成が必要、キャッシュが効きにくい |

本アプリでは音声は一般公開コンテンツのため、**パブリックアクセス方式を推奨**します。

---

## コスト見積もり

| 項目 | 月額目安 |
|------|---------|
| R2 ストレージ（数百MB程度） | 無料枠内（10GB/月まで無料） |
| R2 クラスB操作（読み取り） | 無料枠内（1,000万回/月まで無料） |
| R2 データ転送（エグレス） | **無料**（R2最大の利点） |
| **合計** | **$0**（無料枠内で収まる見込み） |

> **参考**: Cloudflare R2 は 10GB のストレージと 1,000万回のクラスB操作（読み取り）が無料で含まれます。音声ファイル15本（数百MB）のホスティングであれば無料枠内で十分運用可能です。

---

## 実装フェーズ

### Phase 1: インフラ準備

1. Cloudflare R2 バケットの作成
2. パブリックアクセス（R2.dev サブドメイン）の有効化
3. 音声ファイルの `.mp3` 変換
4. R2 に音声ファイルをアップロード
5. `composer require league/flysystem-aws-s3-v3`
6. `config/filesystems.php` の `throw`/`report` を `true` に変更
7. `config/services.php` にマインドフルネス設定追加
8. `.env.example` に環境変数追加
9. （任意）ローカルファイルを使う場合のみ `.gitignore` に `public/audio/` 追加

### Phase 2: バックエンド実装

1. `GetAudioUrlRequest`（Form Request）の作成
2. `MindfulnessController` の作成
3. `routes/api.php` に API ルート追加

### Phase 3: フロントエンド実装

1. `mindfulness.blade.php` の作成（Alpine.js プレイヤー）
2. ナビゲーションメニューにリンク追加
3. トップ画面にカード追加

### Phase 4: テスト・デプロイ

1. ローカル環境でのテスト
2. Heroku に環境変数をセット
3. 本番デプロイ

---

## やらないこと

- **ユーザーごとの再生履歴の保存**: DB設計は不要（静的ファイル再生のみ）
- **音声ファイルのアップロード機能**: 音声は事前にR2ダッシュボードから配置する
- **バックグラウンド再生制御**: ブラウザ標準の挙動に委ねる
- **音量調整UI**: ブラウザ / 端末のボリュームに委ねる

---

## 関連ドキュメント

- [DEVELOPMENT_PLAN.md](./DEVELOPMENT_PLAN.md) - 開発計画
- [HEROKU_PIPELINE_AND_REVIEW_APPS_SETUP.md](./HEROKU_PIPELINE_AND_REVIEW_APPS_SETUP.md) - Herokuパイプライン設定
- [DIRECTORY_STRUCTURE.md](./DIRECTORY_STRUCTURE.md) - ディレクトリ構成

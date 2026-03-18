# 会員登録・認証機能 実装プラン

## 1. 概要

本ドキュメントは、メンタルセルフケアアプリに会員登録・ログイン・退会機能を実装するための設計と手順をまとめたものです。

### 1.1 現状の課題

- 認証方式が HTTP Basic 認証（環境変数制御）であり、ユーザー管理ができていない
- 全データテーブル（copings, columns, writing_disclosures 等）に `user_id` カラムがなく、データがグローバルに共有されている
- マルチユーザー対応が不可能な状態

### 1.2 ゴール

- HTTP Basic 認証を廃止し、会員登録ベースの認証に移行する
- 会員登録、ログイン、退会（アカウント削除）機能を実装する
- 将来的な管理者ユーザー・専門医ユーザーのログイン機能追加を見据え、ユーザー種別ごとにテーブルを分離できる拡張可能な設計にする
- 各ユーザーのデータを `user_id` で分離し、マルチユーザー対応を実現する

---

## 2. 技術選定

### 2.1 認証パッケージ

| パッケージ | 用途 | 採用理由 |
|-----------|------|---------|
| **Laravel Fortify** | 会員登録・ログイン・パスワードリセット等のバックエンドロジック | ヘッドレス認証バックエンド。UIに依存せず、現在の Blade + Vanilla JS 構成でも、将来の React 移行後でも同じバックエンドを利用可能 |
| **Laravel Sanctum** | API 認証（SPA認証 / トークン認証） | 現在の構成（Blade から API を呼び出す SPA 風アーキテクチャ）に最適。Cookie ベースの SPA 認証をサポートし、将来の React Native 移行時にはトークンベース認証に切り替え可能 |

### 2.2 選定理由の補足

- **Fortify を選ぶ理由**: Laravel Breeze や Jetstream は UI を含むスターターキットだが、本プロジェクトは独自の Blade テンプレートを持っているため、バックエンドロジックのみ提供する Fortify が最適
- **Sanctum を選ぶ理由**: 現在のフロントエンドは Blade から `fetch()` で API を呼ぶ構成。Sanctum の SPA 認証モード（Cookie + セッション）を使えば、CSRF トークンを取得するだけで認証済みAPI呼び出しが可能。将来 React Native に移行する際は、トークンベース認証にスムーズに切り替えられる

---

## 3. 認証アーキテクチャ設計

### 3.1 ユーザー種別ごとのテーブル分離設計

一般会員・管理者・専門医はそれぞれ必要なカラムが異なる可能性が高いため、ユーザー種別ごとに独立したテーブルを持つ設計とします。

```
認証テーブル構成（ユーザー種別ごとに独立）
├── users           ... 一般会員（今回実装）
├── admin_users     ... 管理者（将来追加）
└── specialist_users ... 専門医（将来追加）
```

#### 設計判断

| 方式 | メリット | デメリット | 採用 |
|------|---------|----------|------|
| ユーザー種別ごと別テーブル方式 | 種別ごとに独立したカラム設計が可能、関心の分離が明確 | テーブル・モデル・認証ガードが種別分必要 | **○ 採用** |
| 単一テーブル + `role` カラム方式 | シンプル、テーブルが1つで済む | 種別固有カラムが増えると NULL だらけになる | × |
| 単一テーブル + プロフィールテーブル方式 | 認証は共通化できる | JOIN が増える、結局テーブルは増える | × |

#### 採用理由

- 一般会員・管理者・専門医では、今後必要になるプロフィール情報やカラムが大きく異なる可能性が高い
- 単一テーブルに全種別のカラムを詰め込むと、NULL カラムが増えてスキーマが煩雑になる
- 別テーブルにすれば、各種別のスキーマを独立して進化させられる
- Laravel のマルチガード認証により、テーブルが分かれていても認証処理は統一的に扱える

#### `users` テーブル名を変更しない理由

一般会員テーブルの名前は Laravel 標準の `users` をそのまま使用します。

- Laravel のデフォルト設定（`config/auth.php`、`password_reset_tokens`、`sessions` テーブル等）が `users` を前提としており、変更すると多数の設定修正が必要になる
- Fortify / Sanctum もデフォルトで `users` テーブルを参照する
- 将来追加する管理者・専門医テーブルは `admin_users` / `specialist_users` と命名し、`users` は一般会員専用テーブルであることをドキュメントとコードで明示する

### 3.2 認証フロー設計

#### 会員登録フロー

```
[ユーザー] → [登録フォーム (Blade)] → [POST /register]
    → [Fortify: CreateNewUser Action]
        → [バリデーション]
        → [User モデル作成]
        → [自動ログイン]
    → [リダイレクト → ホーム画面]
```

#### ログインフロー

```
[ユーザー] → [ログインフォーム (Blade)] → [POST /login]
    → [Fortify: 認証処理]
        → [メール + パスワード検証]
        → [セッション発行]
    → [リダイレクト → ホーム画面]
```

#### ログアウトフロー

```
[ユーザー] → [ログアウトボタン] → [POST /logout]
    → [Fortify: セッション破棄]
    → [リダイレクト → ログイン画面]
```

#### 退会（アカウント無効化）フロー

```
[ユーザー] → [退会ページ] → [パスワード再入力で確認]
    → [POST /account/delete]
        → [パスワード検証]
        → [User レコードをソフトデリート（deleted_at に日時を記録）]
        → [セッション破棄]
    → [リダイレクト → トップページ（退会完了メッセージ）]
```

#### API 認証フロー（SPA 認証）

```
[Blade ページロード] → [GET /sanctum/csrf-cookie]
    → [CSRF トークン取得]
    → [API 呼び出し (fetch) に Cookie が自動付与]
    → [Sanctum ミドルウェアがセッション認証を検証]
    → [認証済み: API レスポンス返却]
    → [未認証: 401 → ログインページにリダイレクト]
```

### 3.3 DDD レイヤー構成への組み込み

```
app/
├── Domain/
│   ├── Entity/
│   │   └── User.php                          # ユーザーエンティティ（新規）
│   ├── ValueObject/
│   │   └── Email.php                          # メールアドレス値オブジェクト（新規）
│   └── Repository/
│       └── UserRepositoryInterface.php        # ユーザーリポジトリIF（新規）
├── Application/
│   ├── UseCase/
│   │   └── Auth/
│   │       ├── DeleteAccountUseCase.php       # 退会ユースケース（新規）
│   │       └── DeleteAccountInput.php         # 退会入力DTO（新規）
│   └── Service/
│       └── AuthService.php                    # 認証関連サービス（新規、将来的に必要に応じて）
├── Infrastructure/
│   ├── Repository/
│   │   └── EloquentUserRepository.php         # ユーザーリポジトリ実装（新規）
│   └── Database/
│       └── Models/
│           └── User.php                       # Eloquent User モデル（既存を移動・拡張）
└── Http/
    ├── Controllers/
    │   └── Auth/
    │       ├── AccountDeletionController.php   # 退会コントローラー（新規）
    │       └── (Fortify が登録・ログインを処理)
    ├── Requests/
    │   └── Auth/
    │       └── DeleteAccountRequest.php        # 退会リクエスト（新規）
    └── Middleware/
        └── (HttpBasicAuth.php は本番確認後に削除)
```

**補足**: 会員登録・ログイン・ログアウトは Fortify が提供するルートとアクションで処理するため、これらのコントローラーは新規作成不要です。退会機能のみカスタム実装が必要です。

---

## 4. データベース設計変更

### 4.1 `users` テーブル（一般会員）

`users` テーブルは **一般会員専用** のテーブルとして使用します。Laravel 標準の構成に `deleted_at` カラムを追加します（退会時のソフトデリート用）。

| カラム | 型 | 備考 |
|-------|-----|------|
| id | bigint | 主キー |
| name | varchar | |
| email | varchar | UNIQUE |
| email_verified_at | timestamp | NULL可 |
| password | varchar | |
| remember_token | varchar | NULL可 |
| **deleted_at** | **timestamp** | **NULL可（ソフトデリート用、新規追加）** |
| created_at / updated_at | timestamp | |

将来、管理者・専門医が必要になった場合は `admin_users` / `specialist_users` テーブルを別途作成します（詳細はセクション 6 を参照）。

### 4.2 `user_id` カラムの追加対象テーブル

以下のテーブルに `user_id` カラムを追加します。各テーブルは一般会員個人のデータを保持するため、`users` テーブル（一般会員）との紐付けが必須です。

#### 追加対象（直接 `user_id` を追加するテーブル）

| テーブル名 | 追加理由 |
|-----------|---------|
| `copings` | ユーザー個人のコーピングリスト |
| `columns` | ユーザー個人の認知行動療法記録 |
| `writing_disclosures` | ユーザー個人の筆記開示記録 |
| `problem_solvings` | ユーザー個人の問題解決法記録 |
| `simple_notepads` | ユーザー個人のメモ |
| `stressor_and_responses` | ユーザー個人のストレッサー記録 |
| `support_networks` | ユーザー個人のサポートネットワーク |
| `early_maladaptive_schemas` | ユーザー個人のスキーマ記録 |
| `schema_mode_monitorings` | ユーザー個人のセルフモニタリング記録 |
| `safe_places` | ユーザー個人の安全なイメージ |
| `chronologies` | ユーザー個人の年表 |
| `mode_maps` | ユーザー個人のモードマップ |
| `happy_schema_action_plans` | ユーザー個人のハッピースキーマと行動計画 |
| `dialogue_works` | ユーザー個人の対話ワーク記録 |
| `healthy_adult_mode_images` | ユーザー個人のヘルシーな大人モードのイメージ |

#### 追加不要のテーブル

| テーブル名 | 理由 |
|-----------|------|
| `coping_tags` | コーピング専用のシステム共通タグ。全ユーザーで共有 |
| `tags` | 汎用システム共通タグ。全ユーザーで共有 |
| `coping_coping_tag` | 中間テーブル。`copings` 側の `user_id` で間接的に制御 |
| `stressor_and_response_tag` | 中間テーブル。親テーブル側の `user_id` で間接的に制御 |
| `column_tag` | 中間テーブル。親テーブル側の `user_id` で間接的に制御 |
| `problem_solving_tag` | 中間テーブル。親テーブル側の `user_id` で間接的に制御 |
| `problem_solving_plans` | 子テーブル。`problem_solvings` 側の `user_id` で間接的に制御 |
| `problem_solving_solutions` | 子テーブル。`problem_solvings` 側の `user_id` で間接的に制御 |

#### `user_id` カラムの仕様

```
- カラム名: user_id
- 型: bigint (unsigned)
- NOT NULL
- 外部キー → users.id（ON DELETE CASCADE）
- インデックス付与（検索パフォーマンス向上）
```

**マイグレーションファイル例（1テーブル分）:**

```php
Schema::table('copings', function (Blueprint $table) {
    $table->foreignId('user_id')
        ->after('id')
        ->constrained()
        ->cascadeOnDelete();
});
```

**ON DELETE CASCADE の補足**: 退会処理ではソフトデリート（論理削除）を使用するため、通常の退会フローでは CASCADE は発動しません。CASCADE はあくまで、将来的にデータベースレベルで物理削除が必要になった場合（例: GDPR 対応、手動データ整理等）のセーフティネットとして設定します。

### 4.3 マイグレーションファイルの構成

```
database/migrations/
├── xxxx_xx_xx_000001_add_soft_deletes_to_users_table.php
└── xxxx_xx_xx_000002_add_user_id_to_user_data_tables.php
```

- `add_soft_deletes_to_users_table`: `users` テーブルに `deleted_at` カラムを追加（ソフトデリート対応）
- `add_user_id_to_user_data_tables`: 一般会員のデータを保持する15テーブルに `user_id` カラムを追加

---

## 5. 実装手順

### Phase 1: 基盤準備

#### 1-1. パッケージインストール

```bash
docker compose exec app composer require laravel/fortify laravel/sanctum
```

#### 1-2. Fortify の設定

```bash
docker compose exec app php artisan fortify:install
```

`config/fortify.php` の設定:

```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::updatePasswords(),
],
'views' => true,  // Blade ビューを使用
```

#### 1-3. Sanctum の設定

```bash
docker compose exec app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

`config/sanctum.php` の設定:

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,localhost:8081,127.0.0.1')),
```

### Phase 2: データベースマイグレーション

#### 2-1. マイグレーション作成・実行

```bash
docker compose exec app php artisan make:migration add_soft_deletes_to_users_table
docker compose exec app php artisan make:migration add_user_id_to_user_data_tables
docker compose exec app php artisan migrate
```

### Phase 3: ベーシック認証の無効化

#### 3-1. ベーシック認証を無効にする

`.env` で `USE_BASIC_AUTH=false` に設定し、ベーシック認証を無効化します。

```env
USE_BASIC_AUTH=false
```

**注意**: この時点ではベーシック認証のコード（`HttpBasicAuth` ミドルウェア、`bootstrap/app.php` の登録）は削除しません。コードは残したまま環境変数で無効化するだけにとどめます。

#### 3-2. ベーシック認証コードの完全削除（本番リリース後）

本番環境で会員登録・ログイン機能の動作確認が完了し、問題がないことを確認した後に、以下を実施します。

- `app/Http/Middleware/HttpBasicAuth.php` を削除
- `bootstrap/app.php` から `HttpBasicAuth` ミドルウェアの登録を削除
- `.env` から `USE_BASIC_AUTH`、`BASIC_AUTH_USERNAME`、`BASIC_AUTH_PASSWORD` を削除

### Phase 4: 認証機能の実装

#### 4-1. User モデルの拡張

```php
// app/Models/User.php（または移行先）
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

**SoftDeletes の補足**: 退会時は `deleted_at` に日時が記録されるだけで、レコードは物理削除されません。`SoftDeletes` トレイトにより、通常のクエリでは退会済みユーザーは自動的に除外されます。`users` テーブルに `deleted_at` カラムを追加するマイグレーションが必要です。

#### 4-2. Fortify Actions の設定

`app/Actions/Fortify/CreateNewUser.php`:

```php
public function create(array $input): User
{
    Validator::make($input, [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
    ])->validate();

    return User::create([
        'name' => $input['name'],
        'email' => $input['email'],
        'password' => Hash::make($input['password']),
    ]);
}
```

#### 4-3. ルーティングの設定

```php
// routes/web.php

// 未認証ユーザー向け
Route::middleware('guest')->group(function () {
    Route::get('/login', ...);
    Route::get('/register', ...);
});

// 認証必須ルート
Route::middleware('auth')->group(function () {
    // 既存の全ルートをここに移動
    Route::get('/', function () { return view('home'); });
    Route::get('/copings', ...);
    // ... 以下同様
});
```

```php
// routes/api.php

// Sanctum ミドルウェアで保護
Route::middleware('auth:sanctum')->group(function () {
    // 既存の全 API ルートをここに移動
    Route::get('/copings', [CopingController::class, 'index']);
    // ... 以下同様
});
```

#### 4-4. 退会機能の実装

```php
// app/Http/Controllers/Auth/AccountDeletionController.php
public function destroy(DeleteAccountRequest $request): RedirectResponse
{
    $user = $request->user();

    $user->delete(); // SoftDeletes により deleted_at に日時が記録される（データは残る）
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('message', '退会処理が完了しました。');
}
```

**ソフトデリート（論理削除）の採用理由**:
- 退会時にユーザーデータを物理削除しない。`User` モデルに `SoftDeletes` トレイトを使用し、`deleted_at` カラムに日時を記録するのみとする
- ユーザーに紐づくデータ（copings, columns 等）もそのまま残る
- 誤退会時のデータ復旧が可能
- ON DELETE CASCADE は外部キー制約として設定するが、退会処理では物理削除を実行しないため CASCADE は発動しない。CASCADE はあくまで、万が一の手動データ整理時のセーフティネットとして機能する

**処理順序の補足**: `$user->delete()`（ソフトデリート）を `Auth::logout()` より先に実行しています。万が一 `delete()` が失敗した場合、ユーザーはログイン状態のままエラーを確認しリトライできます。

#### 4-5. Blade ビューの作成

新規作成するビュー:

| ビュー | パス | 用途 |
|--------|------|------|
| ログイン画面 | `resources/views/auth/login.blade.php` | メール・パスワードでのログイン |
| 会員登録画面 | `resources/views/auth/register.blade.php` | 名前・メール・パスワードでの登録 |
| パスワードリセット | `resources/views/auth/forgot-password.blade.php` | パスワードリセットメール送信 |
| パスワードリセット（入力） | `resources/views/auth/reset-password.blade.php` | 新パスワード入力 |
| 退会確認画面 | `resources/views/auth/delete-account.blade.php` | パスワード再入力で退会確認 |

### Phase 5: 既存コードの修正

#### 5-1. リポジトリ・UseCase の修正

全てのリポジトリとユースケースに `user_id` のフィルタリングを追加します。

**リポジトリ例（コーピング）:**

```php
// app/Infrastructure/Repository/EloquentCopingRepository.php
public function findAllByUserId(int $userId): array
{
    return CopingModel::where('user_id', $userId)
        ->orderByDesc('point')
        ->orderByDesc('created_at')
        ->get()
        ->map(fn ($model) => $this->toEntity($model))
        ->toArray();
}
```

**コントローラー例:**

```php
public function index(Request $request)
{
    $userId = $request->user()->id;
    $copings = $this->copingRepository->findAllByUserId($userId);
    // ...
}
```

#### 5-2. `user_id` フィルタリングの方式

`user_id` のフィルタリングは **リポジトリ層で明示的に行う方式** を採用します。

**Eloquent グローバルスコープを採用しない理由:**

1. **`user_id` を持たないテーブルが存在する**: `tags`、`coping_tags` はシステム共通データであり `user_id` を持ちません。中間テーブル（`coping_coping_tag`、`column_tag` 等）や子テーブル（`problem_solving_plans`、`problem_solving_solutions`）にも `user_id` はありません。グローバルスコープを一律適用すると、これらのテーブルへのクエリでエラーが発生するか、データが取得できなくなります
2. **将来の管理者ガードとの競合**: 管理者ユーザー（`admin_users` テーブル / `admin` ガード）が追加された場合、`auth()->check()` が管理者コンテキストで意図しない挙動になる可能性がある

**リポジトリ層での明示的フィルタリングの方針:**

```php
// user_id を持つテーブルのリポジトリ → user_id でフィルタリング
$copings = CopingModel::where('user_id', $userId)->get();

// user_id を持たないテーブル（tags, coping_tags）→ フィルタリング不要
$tags = TagModel::all();

// 中間テーブル → 親テーブルの user_id で間接的に制御
$copingTags = $coping->tags; // coping 自体が user_id でフィルタ済み
```

### Phase 6: テスト

#### 6-1. テスト項目

| カテゴリ | テスト内容 |
|---------|----------|
| 会員登録 | 正常系: 名前・メール・パスワードで登録成功、自動ログイン |
| 会員登録 | 異常系: 重複メール、バリデーションエラー |
| ログイン | 正常系: 正しい認証情報でログイン成功 |
| ログイン | 異常系: 誤ったパスワード、存在しないメール |
| ログアウト | 正常系: セッション破棄、ログイン画面にリダイレクト |
| 退会 | 正常系: パスワード確認後、アカウントがソフトデリートされログイン不可になること |
| 退会 | 異常系: パスワード不一致で拒否 |
| API認証 | 認証済み: API にアクセス可能 |
| API認証 | 未認証: 401 レスポンス |
| データ分離 | 他ユーザーのデータが閲覧・編集・削除できないこと |

---

## 6. 管理者・専門医ユーザー対応（将来計画）

現時点では一般会員（`users` テーブル）のみの実装ですが、将来的に管理者・専門医が必要になった場合、それぞれ独立したテーブル・モデル・認証ガードを追加します。

### 6.1 テーブル設計

#### `admin_users` テーブル（管理者）

```
admin_users
├── id — bigint, 主キー
├── name — varchar, NOT NULL
├── email — varchar, UNIQUE, NOT NULL
├── password — varchar, NOT NULL
├── remember_token — varchar, NULL可
├── created_at / updated_at — timestamp
└── （管理者固有のカラムを必要に応じて追加）
```

#### `specialist_users` テーブル（専門医）

```
specialist_users
├── id — bigint, 主キー
├── name — varchar, NOT NULL
├── email — varchar, UNIQUE, NOT NULL
├── password — varchar, NOT NULL
├── remember_token — varchar, NULL可
├── created_at / updated_at — timestamp
└── （専門医固有のカラムを必要に応じて追加。例: 資格情報、専門分野 等）
```

### 6.2 Laravel マルチガード認証の設定

各ユーザー種別に専用の認証ガードとプロバイダを追加します。

```php
// config/auth.php

'guards' => [
    'web' => [                          // 一般会員用（既存）
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [                        // 管理者用（将来追加）
        'driver' => 'session',
        'provider' => 'admin_users',
    ],
    'specialist' => [                   // 専門医用（将来追加）
        'driver' => 'session',
        'provider' => 'specialist_users',
    ],
],

'providers' => [
    'users' => [                        // 一般会員（既存）
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'admin_users' => [                  // 管理者（将来追加）
        'driver' => 'eloquent',
        'model' => App\Models\AdminUser::class,
    ],
    'specialist_users' => [             // 専門医（将来追加）
        'driver' => 'eloquent',
        'model' => App\Models\SpecialistUser::class,
    ],
],
```

### 6.3 拡張時の作業手順

1. テーブルのマイグレーション作成（`admin_users` or `specialist_users`）
2. `Authenticatable` を継承した Eloquent モデル作成（`AdminUser` or `SpecialistUser`）
3. `config/auth.php` にガードとプロバイダを追加
4. 専用のログイン画面・コントローラーを作成
5. ルートに `auth:admin` や `auth:specialist` ミドルウェアを適用
6. 現在の管理画面 URL（`/siteAdmPanel63/*`）を `auth:admin` ミドルウェアで保護

---

## 7. 既存データのマイグレーション手順（開発者用）

現在、開発者（あなた）のみがユーザーであり、全データがグローバルに存在しています。以下の手順で、既存データをあなたの会員アカウントに紐付けます。

### 7.1 前提条件

- Docker 環境が起動していること
- 既存のデータが保持されていること

### 7.2 手順

#### Step 1: 開発者の会員アカウントを作成

Tinker（Laravel の対話型コンソール）を使ってアカウントを作成します。

```bash
docker compose exec app php artisan tinker
```

Tinker 内で以下を実行:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::create([
    'name' => 'あなたの名前',
    'email' => 'your-email@example.com',
    'password' => Hash::make('your-secure-password'),
]);

echo "作成されたユーザーID: " . $user->id;
// → 出力される ID をメモしてください（例: 1）
```

#### Step 2: マイグレーション実行（`user_id` カラム追加）

`user_id` カラムを追加するマイグレーションでは、既存データの `user_id` を自動的に設定する処理を含めます。

**マイグレーションファイルの実装方針:**

```php
// add_user_id_to_user_data_tables マイグレーション内

public function up(): void
{
    $tables = [
        'copings',
        'columns',
        'writing_disclosures',
        'problem_solvings',
        'simple_notepads',
        'stressor_and_responses',
        'support_networks',
        'early_maladaptive_schemas',
        'schema_mode_monitorings',
        'safe_places',
        'chronologies',
        'mode_maps',
        'happy_schema_action_plans',
        'dialogue_works',
        'healthy_adult_mode_images',
    ];

    // 最初のユーザー（開発者）の ID を取得
    $defaultUserId = DB::table('users')->first()?->id;

    foreach ($tables as $tableName) {
        Schema::table($tableName, function (Blueprint $table) {
            // まず NULL 許容でカラムを追加
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });

        // 既存レコードにデフォルトの user_id を設定
        if ($defaultUserId) {
            DB::table($tableName)->whereNull('user_id')->update(['user_id' => $defaultUserId]);
        }

        Schema::table($tableName, function (Blueprint $table) {
            // NOT NULL 制約を適用
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            // 外部キー制約を追加
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            // インデックスを追加
            $table->index('user_id');
        });
    }
}
```

```bash
docker compose exec app php artisan migrate
```

#### Step 3: データの紐付けを確認

```bash
docker compose exec app php artisan tinker
```

```php
// user_id を追加した全15テーブルの紐付けを確認
$tables = [
    'copings',
    'columns',
    'writing_disclosures',
    'problem_solvings',
    'simple_notepads',
    'stressor_and_responses',
    'support_networks',
    'early_maladaptive_schemas',
    'schema_mode_monitorings',
    'safe_places',
    'chronologies',
    'mode_maps',
    'happy_schema_action_plans',
    'dialogue_works',
    'healthy_adult_mode_images',
];

foreach ($tables as $table) {
    $count = DB::table($table)->count();
    $withUserId = DB::table($table)->whereNotNull('user_id')->count();
    echo "{$table}: 全{$count}件中、user_id設定済み {$withUserId}件\n";
}
```

全件が `user_id` 設定済みであることを確認してください。

#### Step 4: 動作確認

1. ブラウザで `http://localhost:8081` にアクセス
2. ログイン画面が表示されることを確認
3. Step 1 で作成した認証情報でログイン
4. 既存のデータ（コーピング、コラム等）が正常に表示されることを確認
5. 新規データの作成・編集・削除が正常に動作することを確認

---

## 8. セキュリティ考慮事項

| 項目 | 対策 |
|------|------|
| パスワードハッシュ | Laravel 標準の bcrypt / argon2 を使用 |
| CSRF 対策 | Laravel 標準の CSRF トークン検証 |
| セッション管理 | セッション固定攻撃対策（ログイン時にセッション再生成） |
| レート制限 | Fortify 標準のログイン試行回数制限（5回/分） |
| パスワード要件 | `Password::defaults()` による最低要件（8文字以上等） |
| データアクセス制御 | 全 API で `user_id` によるフィルタリングを徹底 |
| 退会処理 | ソフトデリート（論理削除）を使用。データは残り、復旧可能 |

---

## 9. 実装優先順位とフェーズ分け

| フェーズ | 内容 | 依存関係 |
|---------|------|---------|
| **Phase 1** | パッケージインストール、設定ファイル | なし |
| **Phase 2** | DB マイグレーション（user_id追加、既存データ紐付け） | Phase 1 |
| **Phase 3** | ベーシック認証無効化（コードは残す。本番確認後に削除） | Phase 2 |
| **Phase 4** | 会員登録・ログイン・ログアウト実装（Fortify） | Phase 3 |
| **Phase 4.5** | API 認証（Sanctum SPA 認証）適用 | Phase 4 |
| **Phase 5** | 退会機能実装 | Phase 4 |
| **Phase 6** | 既存リポジトリ・UseCase・コントローラーの user_id 対応 | Phase 4.5 |
| **Phase 7** | Blade ビュー作成（ログイン、登録、退会画面） | Phase 4 |
| **Phase 8** | テスト作成・実行 | Phase 6, 7 |

---

## 10. 影響範囲まとめ

### 新規作成するファイル

- マイグレーションファイル × 2（users に deleted_at 追加、データテーブルに user_id 追加）
- Fortify Actions（CreateNewUser, UpdateUserPassword 等）
- 退会コントローラー、リクエスト、ユースケース
- 認証関連 Blade ビュー × 5
- ドメインエンティティ・値オブジェクト（User, Email）
- ユーザーリポジトリ（IF + 実装）

### 変更するファイル

- `app/Models/User.php` — `$fillable` の確認
- `bootstrap/app.php` — Sanctum ミドルウェア追加（BasicAuth の削除は本番確認後）
- `routes/web.php` — 認証ミドルウェアでルートを保護
- `routes/api.php` — Sanctum ミドルウェアでルートを保護
- `config/fortify.php` — 機能設定
- `config/sanctum.php` — SPA ドメイン設定
- 全リポジトリ実装（15ファイル） — `user_id` フィルタリング追加
- 全ユースケース — `user_id` の受け渡し追加
- 全コントローラー — `$request->user()->id` の取得・受け渡し

### 削除するファイル（本番リリース後、動作確認完了時に実施）

- `app/Http/Middleware/HttpBasicAuth.php`
- `bootstrap/app.php` 内の `HttpBasicAuth` ミドルウェア登録

# メンタルヘルスサポートアプリ ディレクトリ構成案

## 概要

ドメインモデル仕様書に基づいて、DDD（ドメイン駆動設計）の原則に従ったLaravelプロジェクトのディレクトリ構成を提案します。

## 全体構成図

```
app/
├── Domain/                     # ドメイン層
├── Application/               # アプリケーション層
├── Infrastructure/           # インフラストラクチャ層
└── Http/                    # プレゼンテーション層（既存拡張）
```

## 詳細ディレクトリ構成

### 1. ドメイン層 (`app/Domain/`)

**ビジネスロジックの中核を担う層**

```
app/Domain/
├── Entity/
│   ├── Coping.php                  # コーピングエンティティ
│   ├── CopingTag.php               # コーピングタグエンティティ
│   ├── Column.php                  # コラム（7カラム法）エンティティ
│   ├── WritingDisclosure.php       # 筆記開示エンティティ
│   ├── ProblemSolving.php          # 問題解決法エンティティ
│   ├── ProblemSolvingSolution.php  # 問題解決法の解決策エンティティ
│   └── SimpleNotepad.php           # シンプルメモ帳エンティティ
├── ValueObject/
│   ├── CopingContent.php           # コーピングコンテンツ値オブジェクト
│   ├── WritingDisclosureContent.php # 筆記開示コンテンツ値オブジェクト
│   └── SimpleNotepadContent.php    # シンプルメモ帳コンテンツ値オブジェクト
└── Repository/
    ├── CopingRepositoryInterface.php      # コーピングリポジトリインターフェース
    ├── CopingTagRepositoryInterface.php   # コーピングタグリポジトリインターフェース
    ├── ColumnRepositoryInterface.php      # コラムリポジトリインターフェース
    ├── WritingDisclosureRepositoryInterface.php # 筆記開示リポジトリインターフェース
│   ├── ProblemSolvingRepositoryInterface.php    # 問題解決法リポジトリインターフェース
    └── SimpleNotepadRepositoryInterface.php     # シンプルメモ帳リポジトリインターフェース
```

**特徴：**
- フレームワークに依存しないピュアなビジネスロジック
- エンティティ、値オブジェクト、ドメインサービスを配置
- リポジトリはインターフェースのみ定義

### 2. アプリケーション層 (`app/Application/`)

**ユースケースを実装し、ドメイン層を組み合わせてアプリケーションの機能を提供**

```
app/Application/
├── UseCase/
│   ├── Coping/
│   │   ├── CreateCopingUseCase.php         # コーピング作成
│   │   ├── UpdateCopingUseCase.php         # コーピング更新
│   │   └── DeleteCopingUseCase.php         # コーピング削除
│   ├── Column/
│   │   ├── CreateColumnUseCase.php         # コラム作成
│   │   ├── UpdateColumnUseCase.php         # コラム更新
│   │   └── DeleteColumnUseCase.php         # コラム削除
│   ├── WritingDisclosure/
│   │   ├── CreateWritingDisclosureUseCase.php  # 筆記開示作成
│   │   ├── UpdateWritingDisclosureUseCase.php  # 筆記開示更新
│   │   └── DeleteWritingDisclosureUseCase.php  # 筆記開示削除
│   ├── ProblemSolving/
│   │   ├── CreateProblemSolvingUseCase.php     # 問題解決法作成
│   │   ├── UpdateProblemSolvingUseCase.php     # 問題解決法更新
│   │   ├── DeleteProblemSolvingUseCase.php     # 問題解決法削除
│   │   ├── AddSolutionUseCase.php              # 解決策追加
│   │   ├── UpdateSolutionUseCase.php           # 解決策更新
│   │   └── DeleteSolutionUseCase.php           # 解決策削除
│   └── SimpleNotepad/
│       ├── CreateSimpleNotepadUseCase.php      # シンプルメモ帳作成
│       ├── UpdateSimpleNotepadUseCase.php      # シンプルメモ帳更新
│       └── DeleteSimpleNotepadUseCase.php      # シンプルメモ帳削除
├── DTO/                                    # データ転送オブジェクト
│   ├── CopingData.php                      # コーピングデータ転送用
│   ├── ColumnData.php                      # コラムデータ転送用
│   ├── ProblemSolvingData.php              # 問題解決法データ転送用
│   ├── ProblemSolvingSolutionData.php      # 問題解決法の解決策データ転送用
│   ├── WritingDisclosureData.php           # 筆記開示データ転送用
│   └── SimpleNotepadData.php               # シンプルメモ帳データ転送用
└── Service/
    └── ApplicationService.php              # トランザクション管理、複数ユースケース調整
```

**特徴：**
- 具体的なユースケースの実装
- ドメインサービスを組み合わせてビジネス機能を提供
- コントローラーから呼び出される
- DTOを使用して層間でのデータ受け渡しを明確化

**ApplicationServiceの責務：**
- 複数のユースケース間でのトランザクション管理
- 複数ドメインサービスの協調処理
- 外部APIとの統合処理（メール通知、ログ記録など）
- キャッシュ戦略の実装

### 3. インフラストラクチャ層 (`app/Infrastructure/`)

**外部システムとの連携や永続化を担当**

```
app/Infrastructure/
├── Repository/
│   ├── EloquentCopingRepository.php     # コーピングリポジトリ実装
│   ├── EloquentCopingTagRepository.php  # コーピングタグリポジトリ実装
│   ├── EloquentColumnRepository.php     # コラムリポジトリ実装
│   ├── EloquentWritingDisclosureRepository.php # 筆記開示リポジトリ実装
│   ├── EloquentProblemSolvingRepository.php    # 問題解決法リポジトリ実装
│   └── EloquentSimpleNotepadRepository.php     # シンプルメモ帳リポジトリ実装
├── Providers/
│   └── RepositoryServiceProvider.php    # リポジトリ実装バインド
└── Database/
    └── Models/
        ├── Coping.php                      # コーピングモデル
        ├── CopingTag.php                   # コーピングタグモデル
        ├── Column.php                      # コラムモデル
        ├── WritingDisclosure.php           # 筆記開示モデル
        ├── ProblemSolving.php              # 問題解決法モデル
│       ├── ProblemSolvingSolution.php      # 問題解決法の解決策モデル
        └── SimpleNotepad.php               # シンプルメモ帳モデル
```

**特徴：**
- ドメイン層のリポジトリインターフェースの具体実装
- Eloquentモデル、データベースマイグレーション、ファクトリー、シーダー
- リポジトリ実装でEloquentモデルとドメインエンティティ間の変換を担当

### 4. プレゼンテーション層 (`app/Http/`)

**既存のLaravelのHTTP層を拡張**

```
app/Http/
├── Controllers/
│   ├── CopingController.php        # コーピング操作API
│   ├── CopingTagController.php     # コーピングタグ取得API
│   ├── ColumnController.php        # コラム操作API
│   ├── WritingDisclosureController.php # 筆記開示操作API
│   ├── ProblemSolvingController.php    # 問題解決法操作API
│   └── SimpleNotepadController.php     # シンプルメモ帳操作API
├── Requests/
│   ├── Coping/
│   │   ├── CreateCopingRequest.php
│   │   └── UpdateCopingRequest.php
│   ├── Column/
│   │   ├── CreateColumnRequest.php
│   │   └── UpdateColumnRequest.php
│   ├── WritingDisclosure/
│   │   ├── CreateWritingDisclosureRequest.php
│   │   └── UpdateWritingDisclosureRequest.php
│   ├── ProblemSolving/
│   │   ├── CreateProblemSolvingRequest.php
│   │   ├── UpdateProblemSolvingRequest.php
│   │   ├── AddSolutionRequest.php
│   │   └── UpdateSolutionRequest.php
│   └── SimpleNotepad/
│       ├── CreateSimpleNotepadRequest.php
│       └── UpdateSimpleNotepadRequest.php
└── Resources/
    ├── CopingResource.php          # コーピングレスポンス形式
    ├── CopingTagResource.php       # コーピングタグレスポンス形式
    └── ColumnResource.php          # コラムレスポンス形式
```

**特徴：**
- HTTP リクエストの処理
- バリデーション、レスポンス変換（詳細は `Docs/VALIDATION_GUIDELINES.md` を参照）
- ユースケースの呼び出し
- リクエストをDTOに変換してアプリケーション層に渡す

## Laravel設定変更とファイル移行

### 移行が必要なファイルと設定変更

1. **Eloquentモデルの移行**
   ```php
   // composer.json - オートローダー設定変更
   "autoload": {
       "psr-4": {
           "App\\": "app/",
           "App\\Infrastructure\\Database\\Models\\": "app/Infrastructure/Database/Models/"
       }
   }
   ```

2. **データベース関連ファイルの移行**
   ```php
   // config/database.php - マイグレーションパス設定
   'migrations' => 'app/Infrastructure/Database/Migrations',

   // AppServiceProvider.php - ファクトリーパス設定
   $this->app->make(Factory::class)->load(app_path('Infrastructure/Database/Factories'));
   ```

3. **必要な設定変更**
   ```php
   // config/app.php - サービスプロバイダー追加
   'providers' => [
       // ... 既存プロバイダー
       App\Infrastructure\Providers\DomainServiceProvider::class,
       App\Infrastructure\Providers\RepositoryServiceProvider::class,
   ],
   ```

4. **Artisanコマンド設定**
   ```php
   // app/Console/Kernel.php - カスタムコマンドパス追加
   protected function commands()
   {
       $this->load(__DIR__.'/Commands');
       $this->load(app_path('Infrastructure/Console/Commands'));
   }
   ```

### 設定とサービスプロバイダー

```
app/Infrastructure/
└── Providers/
    ├── DomainServiceProvider.php      # ドメインサービス登録
    └── RepositoryServiceProvider.php  # リポジトリ実装バインド

app/Providers/
└── AppServiceProvider.php         # 既存（フレームワーク標準）
```

## 実装時のポイント

### DDD原則の遵守

1. **依存関係の方向**
   ```
   プレゼンテーション層 → アプリケーション層 → ドメイン層
                                    ↑
                        インフラストラクチャ層
   ```

2. **各層の責務**
   - **ドメイン層**: ビジネスルールとロジック
   - **アプリケーション層**: ユースケースの実装
   - **インフラ層**: 外部システム連携・永続化
   - **プレゼンテーション層**: HTTP API・UI

### 依存性注入の設定

```php
// RepositoryServiceProvider.php
public function register()
{
    $this->app->bind(
        CopingRepositoryInterface::class,
        EloquentCopingRepository::class
    );

    $this->app->bind(
        CopingTagRepositoryInterface::class,
        EloquentCopingTagRepository::class
    );
}
```

### テスト戦略

```
tests/
├── Unit/
│   ├── Domain/           # ドメインロジックのテスト
│   └── Application/      # ユースケースのテスト
├── Feature/
│   └── Http/            # API機能テスト
└── Integration/
    └── Infrastructure/   # リポジトリ実装テスト
```

## メリット

1. **保守性**: 責務が明確に分離されている
2. **テスタビリティ**: 各層を独立してテスト可能
3. **拡張性**: 新機能追加時の影響範囲が限定的
4. **可読性**: ビジネスロジックがドメイン層に集約
5. **境界の明確化**: DTOにより層間のデータ受け渡しが明示的

## 実装順序の提案

### コーピングリスト機能（実装済み）
1. **Phase 1**: Copingエンティティとリポジトリの作成
2. **Phase 2**: マイグレーションとモデルの作成
3. **Phase 3**: ユースケース（作成、更新、削除）の実装
4. **Phase 4**: API エンドポイントの実装

### コラム法機能（実装済み）
1. **Phase 1**: Columnエンティティとリポジトリの作成
2. **Phase 2**: マイグレーションとモデルの作成
3. **Phase 3**: ユースケース（作成、更新、削除）の実装
4. **Phase 4**: API エンドポイントの実装

### 筆記開示機能（実装済み）
1. **Phase 1**: WritingDisclosureエンティティとリポジトリの作成
2. **Phase 2**: マイグレーションとモデルの作成
3. **Phase 3**: ユースケース（作成、更新、削除）の実装
4. **Phase 4**: API エンドポイントの実装

### 問題解決法機能（実装済み）
1. **Phase 1**: ProblemSolving, ProblemSolvingSolutionエンティティとリポジトリの作成
2. **Phase 2**: マイグレーションとモデルの作成
3. **Phase 3**: ユースケース（作成、更新、削除、解決策管理）の実装
4. **Phase 4**: API エンドポイントの実装

### 今後の拡張予定
- コーピングリストの検索・フィルタリング機能
- 認知の歪みパターンの分析機能

この構成により、仕様書で定義されたドメインモデルを適切に実装でき、総合的なメンタルヘルスサポートアプリとして、将来的な機能拡張にも柔軟に対応できます。

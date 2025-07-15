# TODOリストアプリ ディレクトリ構成案

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
│   ├── Todo.php                    # TODOエンティティ
│   ├── Tag.php                     # タグエンティティ
│   └── Difficulty.php              # 難易度エンティティ
├── ValueObject/
│   └── CompletionStatus.php        # 完了状態値オブジェクト
├── Service/
│   ├── CompletedTodoAggregationService.php    # 完了TODO集計サービス
│   ├── AchievementCalculationService.php      # 達成感算出サービス
│   └── TodoSearchService.php                  # TODO検索サービス
└── Repository/
    ├── TodoRepositoryInterface.php        # TODOリポジトリインターフェース
    ├── TagRepositoryInterface.php         # タグリポジトリインターフェース
    └── DifficultyRepositoryInterface.php  # 難易度リポジトリインターフェース
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
│   ├── Todo/
│   │   ├── CreateTodoUseCase.php           # TODO作成
│   │   ├── UpdateTodoUseCase.php           # TODO更新
│   │   ├── CompleteTodoUseCase.php         # TODO完了
│   │   ├── DeleteTodoUseCase.php           # TODO削除
│   │   └── GetTodoListUseCase.php          # TODO一覧取得
│   ├── Tag/
│   │   ├── CreateTagUseCase.php            # タグ作成
│   │   ├── UpdateTagUseCase.php            # タグ更新
│   │   └── DeleteTagUseCase.php            # タグ削除
│   └── Statistics/
│       ├── GetCompletedTodoStatisticsUseCase.php  # 完了TODO統計取得
│       └── SearchCompletedTodosUseCase.php        # 完了TODO検索
├── DTO/                                    # データ転送オブジェクト
│   ├── TodoData.php                        # TODOデータ転送用
│   ├── TagData.php                         # タグデータ転送用
│   └── StatisticsData.php                  # 統計データ転送用
└── Service/
    └── ApplicationService.php              # アプリケーション横断的サービス
```

**特徴：**
- 具体的なユースケースの実装
- ドメインサービスを組み合わせてビジネス機能を提供
- コントローラーから呼び出される
- DTOを使用して層間でのデータ受け渡しを明確化

### 3. インフラストラクチャ層 (`app/Infrastructure/`)

**外部システムとの連携や永続化を担当**

```
app/Infrastructure/
├── Repository/
│   ├── EloquentTodoRepository.php      # TODOリポジトリ実装
│   ├── EloquentTagRepository.php       # タグリポジトリ実装
│   └── EloquentDifficultyRepository.php # 難易度リポジトリ実装
├── Providers/
│   ├── DomainServiceProvider.php       # ドメインサービス登録
│   └── RepositoryServiceProvider.php   # リポジトリ実装バインド
└── Database/
    ├── Models/
    │   ├── Todo.php                        # TODOモデル
    │   ├── Tag.php                         # タグモデル
    │   ├── Difficulty.php                  # 難易度モデル
    │   └── User.php                        # ユーザーモデル（既存から移動）
    ├── Migrations/
    │   ├── create_todos_table.php          # TODOテーブル
    │   ├── create_tags_table.php           # タグテーブル
    │   ├── create_difficulties_table.php   # 難易度テーブル
    │   └── create_todo_tag_table.php       # TODO-タグ中間テーブル
    ├── Factories/
    │   ├── TodoFactory.php
    │   ├── TagFactory.php
    │   ├── DifficultyFactory.php
    │   └── UserFactory.php                # 既存から移動
    └── Seeders/
        ├── TodoSeeder.php
        ├── TagSeeder.php
        ├── DifficultySeeder.php
        └── DatabaseSeeder.php              # 既存から移動
```

**特徴：**
- ドメイン層のリポジトリインターフェースの具体実装
- Eloquentモデル、データベースマイグレーション、ファクトリー、シーダー
- 既存の`app/Models/`からの移行
- リポジトリ実装でEloquentモデルとドメインエンティティ間の変換を担当

### 4. プレゼンテーション層 (`app/Http/`)

**既存のLaravelのHTTP層を拡張**

```
app/Http/
├── Controllers/
│   ├── TodoController.php          # TODO操作API
│   ├── TagController.php           # タグ操作API
│   └── StatisticsController.php    # 統計情報API
├── Requests/
│   ├── Todo/
│   │   ├── CreateTodoRequest.php
│   │   ├── UpdateTodoRequest.php
│   │   └── CompleteTodoRequest.php
│   └── Tag/
│       ├── CreateTagRequest.php
│       └── UpdateTagRequest.php
└── Resources/
    ├── TodoResource.php            # TODOレスポンス形式
    ├── TagResource.php             # タグレスポンス形式
    └── StatisticsResource.php      # 統計情報レスポンス形式
```

**特徴：**
- HTTP リクエストの処理
- バリデーション、レスポンス変換
- ユースケースの呼び出し
- リクエストをDTOに変換してアプリケーション層に渡す

### 5. 設定とサービスプロバイダー

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

### Laravel既存構造との融合

1. **移行が必要なファイル**
   - `app/Models/User.php` → `app/Infrastructure/Database/Models/`
   - `database/migrations/` 配下の全ファイル → `app/Infrastructure/Database/Migrations/`
   - `database/factories/` 配下の全ファイル → `app/Infrastructure/Database/Factories/`
   - `database/seeders/` 配下の全ファイル → `app/Infrastructure/Database/Seeders/`
   - リポジトリ・ドメインサービスバインド用Provider → `app/Infrastructure/Providers/`
   - 新規データベース関連ファイルは`app/Infrastructure/Database/`配下に作成

2. **既存機能の活用**
   - Eloquent ORM → リポジトリ実装で使用
   - フォームリクエスト → バリデーション層として活用、DTOへの変換元
   - リソースクラス → DTOからレスポンス変換として活用

3. **依存性注入の設定**
   ```php
   // RepositoryServiceProvider.php
   $this->app->bind(
       TodoRepositoryInterface::class,
       EloquentTodoRepository::class
   );
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

1. **Phase 1**: ドメイン層の基本エンティティ作成
2. **Phase 2**: インフラ層（マイグレーション、リポジトリ）
3. **Phase 3**: アプリケーション層（ユースケース）
4. **Phase 4**: プレゼンテーション層（API）
5. **Phase 5**: ドメインサービス（統計機能など）

この構成により、仕様書で定義されたドメインモデルを適切に実装でき、将来的な機能拡張にも柔軟に対応できます。

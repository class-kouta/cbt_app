# 達成感と心の健康をサポートするアプリ

## プロジェクト概要

このプロジェクトは、心の健康をサポートするWebアプリケーションです。
認知行動療法（CBT）やコーピングリストなど、メンタルヘルスをサポートする心理療法機能を提供します。

### 主な機能

#### 1. コーピングリスト機能
- ストレス対処法のリスト管理
- お気に入りポイント機能で効果的な対処法を上位表示
- タグによる分類と検索

#### 2. コラム法機能（7カラム法）
- 認知行動療法（CBT）の代表的な技法
- 状況、感情、自動思考、根拠、反証、適応的思考、現在の感情を記録
- 思考パターンの可視化と認知の歪みへの気づきをサポート

## プロジェクトの目的

- 子育てや仕事で忙しい中でも、日々の小さな達成を実感できる
- メンタルヘルスのセルフケアツールとして活用できる
- ネガティブな感情に対処するスキルを身につけられる

## 技術スタック

### バックエンド
- **フレームワーク**: Laravel 11.x
- **アーキテクチャ**: DDD（ドメイン駆動設計）
- **開発手法**: TDD（テスト駆動開発）
- **データベース**: PostgreSQL
- **実行環境**: Docker（開発環境）

### フロントエンド（予定）
- 現在: Blade + Vanilla JavaScript
- 将来: React（Vercelにデプロイ予定）
- さらに将来: React Native（スマホアプリ化）

## セットアップ

### 前提条件
- Docker Desktop がインストールされていること
- Git がインストールされていること

### インストール手順

1. リポジトリのクローン
```bash
git clone <repository-url>
cd <project-directory>
```

2. Docker環境の起動
```bash
docker-compose up -d
```

3. 依存関係のインストール
```bash
docker-compose exec app composer install
```

4. 環境変数ファイルの設定
```bash
cp .env.example .env
docker-compose exec app php artisan key:generate
```

5. データベースのマイグレーション
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

6. アプリケーションへのアクセス
```
http://localhost:8000
```

## 開発について

### ディレクトリ構成
DDDの原則に基づいた4層アーキテクチャを採用しています。

- `app/Domain/` - ドメイン層（ビジネスロジック）
- `app/Application/` - アプリケーション層（ユースケース）
- `app/Infrastructure/` - インフラストラクチャ層（永続化・外部連携）
- `app/Http/` - プレゼンテーション層（API・UI）

詳細は `Docs/DIRECTORY_STRUCTURE.md` を参照してください。

### テスト実行
```bash
docker-compose exec app php artisan test
```

### コーディング規約
- PSR-12に準拠
- DDDの原則を遵守
- TDDによる開発

## ドキュメント

プロジェクトの詳細なドキュメントは `Docs/` ディレクトリにあります。

- [開発計画](Docs/DEVELOPMENT_PLAN.md)
- [ドメインモデル](Docs/DOMAIN_MODEL.md)
- [DB スキーマ](Docs/DB_SCHEMA.md)
- [ディレクトリ構造](Docs/DIRECTORY_STRUCTURE.md)
- [Docker 仕様](Docs/DOCKER_SPEC.md)
- [実装ステップ](Docs/IMPLEMENTATION_STEPS_PHASE2_PROTOTYPE.md)
- [バリデーションガイドライン](Docs/VALIDATION_GUIDELINES.md)

## ロードマップ

### Phase 1: プロトタイプ開発（現在）
- ✅ Docker環境構築
- ✅ コーピングリスト機能
- ✅ コラム法機能
- ✅ 筆記開示機能
- ✅ 問題解決法機能
- 🚧 Herokuへのデプロイ

### Phase 2: CI/CD構築
- GitHub Actions によるCI/CD
- 自動テスト・自動デプロイ

### Phase 3: フロントエンド刷新
- React への移行
- Vercel へのデプロイ

### Phase 4: モバイルアプリ化
- React Native による iOS/Android アプリ開発

## ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## 学習目的

このプロジェクトは以下の技術学習を目的として開発されています：

- TDD（テスト駆動開発）の実践
- DDD（ドメイン駆動設計）の習得
- Docker の運用方法
- React と React Native
- CI/CD パイプラインの構築
- 各種デプロイサービス（Heroku、Vercel）の活用

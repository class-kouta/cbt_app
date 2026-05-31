# スキーマカウント機能 削除調査報告書

**調査日:** 2026-05-31  
**目的:** 既存のスキーマカウント機能を削除するにあたり、修正箇所の洗い出しと影響範囲の特定  
**前提:** 本報告書は調査のみ。コード修正は実施していない。

---

## 1. 機能概要

### 1.1 スキーマカウントとは

「ストレッサーとストレス反応」機能で記録した際に選択した **早期不適応スキーマ（`stimulated_schemas`）** の出現回数を、クライアント側（Alpine.js）で集計・可視化する **閲覧専用のフロントエンド機能** である。

| 項目 | 内容 |
|------|------|
| URL | `/early-maladaptive-schemas/count` |
| ルート名 | `early-maladaptive-schemas.count` |
| ビュー | `resources/views/schema-count.blade.php`（616行） |
| データ取得 | `GET /api/stressor-and-responses`（ページネーションで全件取得） |
| 集計ロジック | ビュー内 JavaScript（`schemaCountApp()`）で実行 |
| 認証 | `auth` + `verified` ミドルウェア配下 |

### 1.2 混同しやすい別機能との区別

本機能は **「早期不適応的スキーマ」機能（`/early-maladaptive-schemas`）とは別物** である。

| 機能 | URL | 用途 | データソース |
|------|-----|------|-------------|
| **スキーマカウント（削除対象）** | `/early-maladaptive-schemas/count` | ストレッサー記録からスキーマ選択回数を集計表示 | `stressor_and_responses.stimulated_schemas` |
| 早期不適応的スキーマ | `/early-maladaptive-schemas` | 18スキーマの囚われ度を記録・編集 | `early_maladaptive_schemas` テーブル |
| ストレッサーとストレス反応 | `/stressor-and-responses` | ストレッサー記録（スキーマ選択含む） | `stressor_and_responses` テーブル |

---

## 2. アーキテクチャ上の位置づけ

```
[ユーザー]
    │
    ├─ ナビ / スキーマ療法トップ ──→ /early-maladaptive-schemas/count
    │                                      │
    │                                      ▼
    │                              schema-count.blade.php
    │                              (Alpine.js: schemaCountApp)
    │                                      │
    │                                      ▼
    └─ ストレッサー記録 ──────────→ GET /api/stressor-and-responses
                                           │
                                           ▼
                                   stressor_and_responses テーブル
                                   (stimulated_schemas カラム)
```

**重要:** スキーマカウント専用の Controller / UseCase / Repository / Model / API エンドポイント / DB テーブル / テストは **存在しない**。削除対象はプレゼンテーション層（ルート・ビュー・ナビゲーション）に限定される。

---

## 3. 修正箇所一覧

### 3.1 削除対象ファイル

| # | ファイル | 行数 | 内容 |
|---|---------|------|------|
| 1 | `resources/views/schema-count.blade.php` | 616行 | スキーマカウント画面本体（UI + Alpine.js 集計ロジック + 18スキーマ定義 + 詳細ツールチップ文言） |

### 3.2 修正対象ファイル

| # | ファイル | 修正内容 | 該当箇所 |
|---|---------|---------|---------|
| 1 | `routes/web.php` | `/count` ルート定義の削除 | 249–251行付近 |
| 2 | `resources/views/layouts/app.blade.php` | サイドナビ `$schemaLinks` からエントリ削除 | 443行付近 |
| 3 | `resources/views/schema-therapy.blade.php` | `$features` 配列からカード定義削除 | 90–98行付近 |

#### 3.2.1 `routes/web.php` 削除対象

```php
Route::get('/count', function () {
    return view('schema-count');
})->name('count');
```

- `early-maladaptive-schemas` プレフィックスグループ内に定義されている
- 同一グループの `Route::get('/', ...)` （早期不適応的スキーマ一覧）は **残す**

#### 3.2.2 `resources/views/layouts/app.blade.php` 削除対象

```php
['label' => 'スキーマカウント', 'patterns' => 'early-maladaptive-schemas/count', 'href' => '/early-maladaptive-schemas/count'],
```

#### 3.2.3 `resources/views/schema-therapy.blade.php` 削除対象

```php
[
    'href' => '/early-maladaptive-schemas/count',
    'title' => 'スキーマカウント',
    'icon' => '...',
],
```

---

## 4. 修正不要（影響を受けない）箇所

以下はスキーマカウント機能と **名称・URL が近い** が、削除の影響を受けない。

### 4.1 バックエンド（DDD 層）

| カテゴリ | ファイル / コンポーネント | 理由 |
|---------|--------------------------|------|
| API | `routes/api.php` の `/early-maladaptive-schemas` 系 | 早期不適応スキーマ CRUD 用。スキーマカウント専用ではない |
| Controller | `EarlyMaladaptiveSchemaController` | 上記 API 用 |
| Controller | `StressorAndResponseController` | ストレッサー機能本体。スキーマカウント以外でも利用 |
| UseCase | `StressorAndResponse/*` | 同上 |
| Repository | `EloquentStressorAndResponseRepository` | 同上 |
| Model | `StressorAndResponse` | 同上 |
| Request | `Create/UpdateStressorAndResponseRequest` | `stimulated_schemas` バリデーションはストレッサー機能用 |
| CSV Export | `ExportStressorAndResponseCsvUseCase` | `stimulated_schemas` を CSV 出力。スキーマカウント非依存 |

### 4.2 データベース

| 対象 | 理由 |
|------|------|
| `stressor_and_responses.stimulated_schemas` カラム | ストレッサー記録機能で引き続き使用。削除不要 |
| マイグレーション `2026_01_02_000002_add_stimulated_schemas_to_stressor_and_responses_table.php` | 上記カラム追加用。ロールバック不要 |
| `early_maladaptive_schemas` テーブル全体 | 別機能。影響なし |

### 4.3 フロントエンド（他画面）

| ファイル | 理由 |
|---------|------|
| `resources/views/stressor-and-responses.blade.php` | スキーマ選択 UI。記録機能の一部 |
| `resources/views/stressor-and-response-detail.blade.php` | 選択スキーマの表示 |
| `resources/views/early-maladaptive-schemas.blade.php` | 早期不適応的スキーマ編集画面。スキーマカウントへのリンクなし |

### 4.4 テスト

| 対象 | 理由 |
|------|------|
| `tests/` 配下全体 | スキーマカウント専用テストは **存在しない**。削除に伴うテスト修正は不要 |

### 4.5 ドキュメント

| ファイル | 状況 |
|---------|------|
| `Docs/DB_SCHEMA.md` | 「スキーマカウント」の記載なし（`stimulated_schemas` カラムの記載も未反映） |
| `Docs/DOMAIN_MODEL.md` | `StressorAndResponse` に `stimulated_schemas` 属性の記載なし |
| `Docs/DEVELOPMENT_PLAN.md` | 関連記載なし |
| `README.md` | 関連記載なし |
| `.cursor/spec/` | 関連記載なし |

→ ドキュメント更新は **必須ではない**（現状ドキュメントにスキーマカウント機能の記述がないため）。

---

## 5. 削除による影響分析

### 5.1 ユーザー向け影響

| 影響 | 深刻度 | 詳細 |
|------|--------|------|
| 集計画面の消失 | 中 | スキーマ選択回数の棒グラフ・ランキング・ツールチップ説明が閲覧不可になる |
| ブックマーク / 外部リンク | 低 | `/early-maladaptive-schemas/count` にアクセスすると 404 になる |
| ストレッサー記録機能 | なし | スキーマ選択・保存・詳細表示は引き続き利用可能 |
| 早期不適応的スキーマ機能 | なし | 囚われ度の記録・編集は影響なし |
| 既存データ | なし | DB データの削除・移行は不要 |

### 5.2 技術的影響

| 観点 | 影響 |
|------|------|
| API 負荷 | 微減。当画面は `/api/stressor-and-responses` を全ページ分ループ取得していた |
| コード量 | 約 620 行削除（ビュー 616 行 + ルート/ナビ数行） |
| ビルド / デプロイ | 通常デプロイのみ。マイグレーション不要 |
| 依存パッケージ | 変更なし |
| ルート名 `early-maladaptive-schemas.count` | コードベース内で `route()` 参照は **0 件**。削除しても参照切れなし |

### 5.3 重複コードに関する補足

`schema-count.blade.php` 内の以下は、他ファイルと **内容的に重複** している。

| 重複内容 | 重複先 |
|---------|--------|
| 18スキーマ定義（key / name / 領域） | `stressor-and-responses.blade.php` |
| スキーマ詳細（belief / behavior / background） | `schema-count.blade.php` のみ（他画面には詳細ツールチップなし） |

→ スキーマカウント削除により、スキーマ詳細説明文（約 90 行）がアプリ内から完全に失われる。ただしこれらは `early-maladaptive-schemas.blade.php` 側にも別形式の説明がある可能性があり、削除対象機能固有のコンテンツである。

---

## 6. 推奨修正手順

影響範囲が小さいため、以下の順序で実施可能。

1. `resources/views/schema-count.blade.php` を削除
2. `routes/web.php` から `/count` ルートを削除
3. `resources/views/layouts/app.blade.php` からナビリンクを削除
4. `resources/views/schema-therapy.blade.php` から機能カードを削除
5. 動作確認
   - `/early-maladaptive-schemas/count` → 404 確認
   - `/schema-therapy` → カードが 8 件に減っていること
   - サイドナビ → 「スキーマカウント」リンクが消えていること
   - `/stressor-and-responses` → スキーマ選択・保存が正常であること
   - `/early-maladaptive-schemas` → 既存機能が正常であること

---

## 7. リスクと注意点

| リスク | 対策 |
|--------|------|
| URL パスが `early-maladaptive-schemas` 配下のため、早期不適応スキーマ機能ごと削除と誤解される | 本報告書 1.2 の区別表を参照。`/early-maladaptive-schemas` ルートは残す |
| `stimulated_schemas` カラムまで削除してしまう | **削除しない**。ストレッサー機能・CSV エクスポートで使用中 |
| 将来スキーマカウントを再実装する場合 | 集計ロジックはビュー内にしか存在しないため、Git 履歴から復元する必要がある |

---

## 8. 調査サマリー

| 項目 | 結果 |
|------|------|
| 修正ファイル数 | **4 ファイル**（削除 1 + 修正 3） |
| バックエンド修正 | **不要** |
| DB マイグレーション | **不要** |
| テスト修正 | **不要**（専用テストなし） |
| ドキュメント修正 | **任意**（現状ドキュメントに機能記載なし） |
| 作業規模 | 小（フロントエンドのルート・ナビ・ビュー削除のみ） |
| 既存データへの影響 | **なし** |

---

## 付録 A: 検索キーワードとヒット結果

調査で使用したキーワードと、スキーマカウント機能に直接関連するヒットファイル。

| キーワード | ヒット（機能関連のみ） |
|-----------|----------------------|
| `schema-count` | `routes/web.php`, `schema-count.blade.php` |
| `schemaCountApp` | `schema-count.blade.php` |
| `early-maladaptive-schemas/count` | `routes/web.php`, `app.blade.php`, `schema-therapy.blade.php` |
| `スキーマカウント` | `schema-count.blade.php`, `app.blade.php`, `schema-therapy.blade.php` |
| `early-maladaptive-schemas.count`（ルート名） | `routes/web.php` のみ（`route()` 参照 0 件） |

## 付録 B: データフロー詳細

```
schemaCountApp.init()
  └─ loadData()
       └─ apiFetch('/api/stressor-and-responses?per_page=100&page=N')  ※全ページループ
            └─ records[].stimulated_schemas (string[] 例: ['abandonment', 'failure'])
  └─ calculateCounts()
       └─ 18スキーマ key ごとに出現回数を集計
  └─ UI 描画
       ├─ 領域別棒グラフ（5領域 × 18スキーマ）
       ├─ ツールチップ（深い思い込み / 典型的な行動 / 背景）
       └─ ランキング（count > 0 のスキーマを降順）
```

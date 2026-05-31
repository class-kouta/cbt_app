# スキーマカウント・刺激されたスキーマ 削除調査報告書

**調査日:** 2026-05-31  
**最終更新:** 2026-05-31（刺激されたスキーマ削除要件・コードレビュー対応を追記）  
**目的:** 以下2点の削除に向け、修正箇所の洗い出しと影響範囲の特定

1. スキーマカウント機能（`/early-maladaptive-schemas/count`）
2. ストレッサーとストレス反応における「刺激されたスキーマ」入力・保存（`stimulated_schemas` カラム含む）

**前提:** 本報告書は調査のみ。コード修正は実施していない。

---

## 1. 削除対象の概要

### 1.1 スキーマカウント機能

「ストレッサーとストレス反応」で記録した `stimulated_schemas` の出現回数を、クライアント側（Alpine.js）で集計・可視化する **閲覧専用のフロントエンド機能**。

| 項目 | 内容 |
|------|------|
| URL | `/early-maladaptive-schemas/count` |
| ルート名 | `early-maladaptive-schemas.count` |
| ビュー | `resources/views/schema-count.blade.php`（616行） |
| データ取得 | `GET /api/stressor-and-responses`（ページネーションで全件取得） |
| 認証 | `auth` + `verified` ミドルウェア配下 |

### 1.2 刺激されたスキーマ（ストレッサー機能内）

ストレッサー記録フォームで18スキーマを複数選択し、`stressor_and_responses.stimulated_schemas`（JSON 配列）に保存する機能。

| 項目 | 内容 |
|------|------|
| 入力 UI | `resources/views/stressor-and-responses.blade.php`（263–710行付近、約450行） |
| 詳細表示 | `resources/views/stressor-and-response-detail.blade.php`（121–162行、コピー機能含む） |
| DB カラム | `stressor_and_responses.stimulated_schemas`（nullable JSON） |
| 追加マイグレーション | `database/migrations/2026_01_02_000002_add_stimulated_schemas_to_stressor_and_responses_table.php` |

### 1.3 混同しやすい別機能との区別

| 機能 | URL | 用途 | データソース | 削除対象 |
|------|-----|------|-------------|---------|
| スキーマカウント | `/early-maladaptive-schemas/count` | スキーマ選択回数の集計表示 | `stimulated_schemas` | **はい** |
| 刺激されたスキーマ（ストレッサー内） | `/stressor-and-responses` | 記録時のスキーマ選択 | `stimulated_schemas` | **はい** |
| 早期不適応的スキーマ | `/early-maladaptive-schemas` | 18スキーマの囚われ度を記録 | `early_maladaptive_schemas` テーブル | **いいえ** |
| ストレッサーとストレス反応（本体） | `/stressor-and-responses` | ストレッサー・認知・感情等の記録 | `stressor_and_responses`（上記カラム除く） | **いいえ** |

---

## 2. `stimulated_schemas` の利用状況調査

### 2.1 コードベース全体の参照一覧

| # | レイヤ | ファイル | 用途 |
|---|--------|---------|------|
| 1 | フロント | `schema-count.blade.php` | 集計データソース（削除対象） |
| 2 | フロント | `stressor-and-responses.blade.php` | 入力 UI・送信・変更検知・コピー（削除対象） |
| 3 | フロント | `stressor-and-response-detail.blade.php` | 詳細表示・コピー（削除対象） |
| 4 | Controller | `StressorAndResponseController.php` | show/store/update の JSON 入出力 |
| 5 | Request | `CreateStressorAndResponseRequest.php` | バリデーション |
| 6 | Request | `UpdateStressorAndResponseRequest.php` | バリデーション |
| 7 | DTO | `StressorAndResponseData.php` | `stimulatedSchemas` プロパティ |
| 8 | Entity | `StressorAndResponse.php` | ドメイン属性・getter |
| 9 | UseCase | `CreateStressorAndResponseUseCase.php` | 作成時の受け渡し |
| 10 | UseCase | `UpdateStressorAndResponseUseCase.php` | 更新時の受け渡し |
| 11 | UseCase | `ExportStressorAndResponseCsvUseCase.php` | CSV「刺激されたスキーマ」列出力 |
| 12 | Repository | `EloquentStressorAndResponseRepository.php` | 永続化・検索結果マッピング |
| 13 | Model | `Infrastructure/Database/Models/StressorAndResponse.php` | `$fillable` / `$casts` |
| 14 | DB | `stressor_and_responses.stimulated_schemas` | カラム本体 |

**結論:** 上記14箇所が `stimulated_schemas` の全参照。**スキーマカウント・ストレッサー UI を削除した後は、CSV エクスポートとバックエンド層のみが残る。** これらも合わせて削除すれば、DB カラムは **他機能からの参照なし** となり、安全に削除可能。

### 2.2 テスト・ドキュメント

| 対象 | 状況 |
|------|------|
| `tests/` | `stimulated_schemas` 関連テスト **0 件** |
| `Docs/DB_SCHEMA.md` | `stimulated_schemas` カラムの記載 **なし** |
| `Docs/DOMAIN_MODEL.md` | `StressorAndResponse` に属性記載 **なし** |

---

## 3. スキーマ詳細説明文の所在調査（コードレビュー対応）

> **レビュー指摘（PR #133）:** `schema-count.blade.php` 削除で失われるスキーマ詳細説明文（約90行）について、`early-maladaptive-schemas.blade.php` 等で同等の説明が提供されているか調査結果を確定させること。

### 3.1 調査結果（確定）

**18スキーマすべて** について、`early-maladaptive-schemas.blade.php` に以下3項目の詳細説明が **HTML として提供されている**（78–728行付近）。

- 深い思い込み
- 典型的な行動・特徴
- 背景・ルーツ

加えて、`stressor-and-responses.blade.php` の刺激されたスキーマ選択 UI 内（318–704行）にも **同一形式・同一内容** の説明文が18スキーマ分埋め込まれている（`early-maladaptive-schemas.blade.php` と文言一致）。

| 画面 | 詳細説明の有無 | 件数 | 備考 |
|------|--------------|------|------|
| `early-maladaptive-schemas.blade.php` | **あり** | 18/18 | 囚われ度入力と併せて常時表示。**正本（canonical）** |
| `stressor-and-responses.blade.php` | **あり** | 18/18 | 選択 UI 内のアコーディオン。`early-maladaptive-schemas` と同一文言 |
| `schema-count.blade.php` | **あり** | 18/18 | JS `schemaDetails` オブジェクト内。一部スキーマで文言が異なる（後述） |
| `stressor-and-response-detail.blade.php` | **なし** | 0/18 | スキーマ名の表示のみ |

### 3.2 `schema-count` と `early-maladaptive-schemas` の文言差分

`schema-count.blade.php` の `schemaDetails` は、以下10スキーマで `early-maladaptive-schemas.blade.php`（および `stressor-and-responses.blade.php`）と **文言が異なる**。

| スキーマ key | 差分の概要 |
|-------------|-----------|
| `enmeshment` | 思い込み・行動・背景すべて異なる表現 |
| `failure` | 思い込み・行動・背景すべて異なる表現 |
| `entitlement_grandiosity` | 思い込み・行動・背景すべて異なる表現 |
| `insufficient_self_control` | 思い込み・行動・背景すべて異なる表現 |
| `self_sacrifice` | 背景・ルーツの記述が異なる |
| `approval_seeking` | 思い込み・行動・背景すべて異なる表現 |
| `negativity_pessimism` | 思い込み・行動・背景すべて異なる表現 |
| `emotional_inhibition` | 思い込み・行動・背景すべて異なる表現 |
| `unrelenting_standards` | 思い込み・行動・背景すべて異なる表現 |
| `punitiveness` | 思い込み・行動・背景すべて異なる表現 |

残り8スキーマ（`abandonment` 〜 `subjugation` のうち上記以外）は **3画面で文言一致**。

### 3.3 レビュー指摘への結論

| 観点 | 結論 |
|------|------|
| 同等の詳細説明は他画面にあるか | **ある。** `early-maladaptive-schemas.blade.php` に18スキーマ分すべて存在 |
| 説明文の移植・統合は必要か | **不要。** 正本は既に `early-maladaptive-schemas.blade.php` に存在 |
| 削除で完全に失われるコンテンツか | **いいえ。** `schema-count` 固有の代替文言（10スキーマ分）のみ失われる。カテゴリ自体（深い思い込み等）は残存 |
| ストレッサー UI 削除時の説明文 | ストレッサー画面内の重複説明（約450行）も削除されるが、`early-maladaptive-schemas.blade.php` に同一内容が残る |

---

## 4. 修正箇所一覧

### 4.1 削除対象ファイル

| # | ファイル | 行数 | 内容 |
|---|---------|------|------|
| 1 | `resources/views/schema-count.blade.php` | 616行 | スキーマカウント画面全体 |

### 4.2 修正対象ファイル — スキーマカウント削除

| # | ファイル | 修正内容 |
|---|---------|---------|
| 1 | `routes/web.php` | `/count` ルート定義の削除（249–251行付近） |
| 2 | `resources/views/layouts/app.blade.php` | サイドナビ `$schemaLinks` から「スキーマカウント」削除（443行付近） |
| 3 | `resources/views/schema-therapy.blade.php` | `$features` 配列からスキーマカウントカード削除（90–98行付近） |

### 4.3 修正対象ファイル — 刺激されたスキーマ削除

#### フロントエンド

| # | ファイル | 修正内容 | 規模感 |
|---|---------|---------|--------|
| 1 | `resources/views/stressor-and-responses.blade.php` | 「刺激されたスキーマ」セクション全体（263–710行）削除。JS から `stimulated_schemas` 関連（formData・toggleSchema・getSchemaName・変更検知・コピー文言）削除 | 大（約500行） |
| 2 | `resources/views/stressor-and-response-detail.blade.php` | 刺激されたスキーマ表示ブロック（121–162行）削除。`getSchemaName`・コピー文言削除 | 小（約50行） |

#### バックエンド

| # | ファイル | 修正内容 |
|---|---------|---------|
| 3 | `app/Http/Controllers/StressorAndResponseController.php` | show/store/update から `stimulated_schemas` の入出力削除 |
| 4 | `app/Http/Requests/StressorAndResponse/CreateStressorAndResponseRequest.php` | バリデーションルール削除 |
| 5 | `app/Http/Requests/StressorAndResponse/UpdateStressorAndResponseRequest.php` | バリデーションルール削除 |
| 6 | `app/Application/DTO/StressorAndResponseData.php` | `stimulatedSchemas` プロパティ削除 |
| 7 | `app/Domain/Entity/StressorAndResponse.php` | 属性・getter・コンストラク引数削除 |
| 8 | `app/Application/UseCase/StressorAndResponse/CreateStressorAndResponseUseCase.php` | 引数受け渡し削除 |
| 9 | `app/Application/UseCase/StressorAndResponse/UpdateStressorAndResponseUseCase.php` | 引数受け渡し削除 |
| 10 | `app/Application/UseCase/StressorAndResponse/ExportStressorAndResponseCsvUseCase.php` | CSV ヘッダー「刺激されたスキーマ」列・`SCHEMA_NAME_MAP`・`translateSchemas()` 削除 |
| 11 | `app/Infrastructure/Repository/EloquentStressorAndResponseRepository.php` | 永続化・マッピングから削除 |
| 12 | `app/Infrastructure/Database/Models/StressorAndResponse.php` | `$fillable` / `$casts` から削除 |

#### データベース

| # | ファイル | 修正内容 |
|---|---------|---------|
| 13 | **新規** `database/migrations/YYYY_MM_DD_HHMMSS_drop_stimulated_schemas_from_stressor_and_responses_table.php` | `stimulated_schemas` カラムを DROP |

※ 既存マイグレーション `2026_01_02_000002_add_stimulated_schemas_to_stressor_and_responses_table.php` は履歴として **変更しない**。

### 4.4 修正不要（影響を受けない）箇所

| 対象 | 理由 |
|------|------|
| `early_maladaptive_schemas` テーブル・API・画面 | 別機能。スキーマ詳細説明の正本として **残す** |
| `routes/api.php` の `/early-maladaptive-schemas` 系 | 上記機能専用 |
| `EarlyMaladaptiveSchemaController` | 同上 |
| ストレッサー機能のその他属性（stressor, cognition, mood 等） | 影響なし |
| `tests/` | 関連テストなし（修正不要だが、API レスポンス変更に伴う回帰確認は推奨） |

---

## 5. 削除による影響分析

### 5.1 ユーザー向け影響

| 影響 | 深刻度 | 詳細 |
|------|--------|------|
| スキーマカウント画面の消失 | 中 | 棒グラフ・ランキングが閲覧不可 |
| ストレッサー記録でのスキーマ選択不可 | 中 | 記録フォームから18スキーマ選択 UI が消える |
| 詳細画面でのスキーマ表示不可 | 低 | 過去記録に保存済みのスキーマ名が表示されなくなる |
| CSV の「刺激されたスキーマ」列消失 | 低 | エクスポート列が1つ減る |
| **既存 DB データの消失** | 中 | マイグレーション実行で `stimulated_schemas` カラム内の全データが **永久削除** される |
| スキーマ詳細説明文 | **なし** | `early-maladaptive-schemas.blade.php` に18スキーマ分すべて残存 |
| 早期不適応的スキーマ機能 | なし | 囚われ度の記録・編集は影響なし |
| ストレッサー記録（本体） | なし | ストレッサー・認知・感情・身体反応・行動・タグは引き続き利用可能 |

### 5.2 技術的影響

| 観点 | 影響 |
|------|------|
| API レスポンス | `GET/POST/PUT /api/stressor-and-responses` から `stimulated_schemas` フィールドが消える |
| API リクエスト | クライアントから `stimulated_schemas` を送信しても無視（削除後はバリデーションも不存在） |
| コード量 | 約 1,200 行以上削除見込み（schema-count 616行 + stressor UI 約500行 + バックエンド約100行） |
| DB マイグレーション | **必要**（カラム DROP） |
| デプロイ | マイグレーション実行が必要。本番では既存データ消失に注意 |
| ルート名 `early-maladaptive-schemas.count` | `route()` 参照 **0 件**。削除しても参照切れなし |

---

## 6. 推奨修正手順

### Phase 1: スキーマカウント削除

1. `resources/views/schema-count.blade.php` を削除
2. `routes/web.php` から `/count` ルートを削除
3. `resources/views/layouts/app.blade.php` からナビリンクを削除
4. `resources/views/schema-therapy.blade.php` から機能カードを削除

### Phase 2: 刺激されたスキーマ削除

5. `stressor-and-responses.blade.php` からスキーマ選択 UI・関連 JS を削除
6. `stressor-and-response-detail.blade.php` からスキーマ表示・関連 JS を削除
7. バックエンド12ファイルから `stimulated_schemas` / `stimulatedSchemas` を削除
8. 新規マイグレーションで `stimulated_schemas` カラムを DROP
9. `php artisan migrate` 実行

### Phase 3: 動作確認

| 確認項目 | 期待結果 |
|---------|---------|
| `/early-maladaptive-schemas/count` | 404 |
| `/schema-therapy` | スキーマカウントカードなし（8件） |
| サイドナビ | 「スキーマカウント」リンクなし |
| `/stressor-and-responses` | スキーマ選択 UI なし。記録・編集・保存が正常 |
| `/stressor-and-responses/{id}` | スキーマ表示なし。他項目は正常 |
| CSV エクスポート | 「刺激されたスキーマ」列なし |
| `/early-maladaptive-schemas` | 18スキーマの説明・囚われ度入力が正常 |
| API レスポンス | `stimulated_schemas` キーが含まれない |

---

## 7. リスクと注意点

| リスク | 対策 |
|--------|------|
| `early-maladaptive-schemas` 機能ごと削除と誤解 | 1.3 の区別表を参照。`/early-maladaptive-schemas` ルート・テーブルは残す |
| 既存 `stimulated_schemas` データの消失 | マイグレーション前に CSV エクスポート等でバックアップを検討（必要に応じて） |
| スキーマ詳細説明文の消失 | **問題なし。** `early-maladaptive-schemas.blade.php` に正本が存在（3.1 参照）。移植不要 |
| `schema-count` 固有の代替文言（10スキーマ）の消失 | 影響軽微。`early-maladaptive-schemas` の正本文言が残る。必要なら Git 履歴から参照可能 |
| 将来の再実装 | 集計ロジックは `schema-count.blade.php` 内のみ。Git 履歴から復元が必要 |

---

## 8. 調査サマリー

| 項目 | 結果 |
|------|------|
| 修正ファイル数 | **16 ファイル**（削除 1 + 修正 14 + 新規マイグレーション 1） |
| バックエンド修正 | **必要**（12ファイル） |
| DB マイグレーション | **必要**（`stimulated_schemas` カラム DROP） |
| テスト修正 | **不要**（専用テストなし。手動/API 確認推奨） |
| ドキュメント修正 | **任意**（`Docs/DB_SCHEMA.md` にカラム記載がないため必須ではない） |
| 作業規模 | **中**（フロント約1,100行 + バックエンド約100行 + マイグレーション） |
| 既存データへの影響 | **あり**（`stimulated_schemas` カラム内データは削除される） |
| コードレビュー（PR #133） | **対応済み**（3章で説明文の所在を確定。移植不要と結論） |

---

## 付録 A: 検索キーワードとヒット結果

| キーワード | ヒットファイル |
|-----------|--------------|
| `schema-count` / `schemaCountApp` | `routes/web.php`, `schema-count.blade.php` |
| `early-maladaptive-schemas/count` / `スキーマカウント` | `routes/web.php`, `app.blade.php`, `schema-therapy.blade.php` |
| `stimulated_schemas` / `stimulatedSchemas` | 14ファイル（2.1 参照） |
| `深い思い込み` | `early-maladaptive-schemas.blade.php`, `stressor-and-responses.blade.php`, `schema-count.blade.php` |

## 付録 B: アーキテクチャ（削除後）

```
[ユーザー]
    │
    ├─ 早期不適応的スキーマ ──→ /early-maladaptive-schemas
    │                              │
    │                              ▼
    │                    early-maladaptive-schemas.blade.php
    │                    （18スキーマ詳細説明 + 囚われ度入力）
    │                              │
    │                              ▼
    │                    early_maladaptive_schemas テーブル
    │
    └─ ストレッサーとストレス反応 ──→ /stressor-and-responses
                                       │
                                       ▼
                              stressor_and_responses テーブル
                              （stressor, cognition, mood, body_reaction, behavior, tags）
                              ※ stimulated_schemas カラムは削除
```

## 付録 C: コードレビュー対応記録

| 項目 | 内容 |
|------|------|
| PR | #133 |
| レビュアー | gemini-code-assist[bot] |
| 指摘内容 | スキーマ詳細説明文の他画面での提供有無を確定させ、必要なら移植を検討すること |
| 対応 | 3章に調査結果を追記。`early-maladaptive-schemas.blade.php` に18/18スキーマ分の同等説明を確認。移植・統合は **不要** と結論 |
| 報告書更新日 | 2026-05-31 |

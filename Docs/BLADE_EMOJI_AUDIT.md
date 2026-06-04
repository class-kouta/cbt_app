# Blade テンプレート内 絵文字使用状況 調査レポート

調査日: 2026-06-03（最終更新: 2026-06-03 — Heroicons 統一・差し替え表追加）  
対象: `resources/views/**/*.blade.php`（全 58 ファイル）  
方法: Unicode 絵文字・記号の機械スキャン + 目視確認

**関連ドキュメント**

- サードパーティ表記（日本語）: [`Docs/THIRD_PARTY_NOTICES.md`](THIRD_PARTY_NOTICES.md)
- MIT 全文（Heroicons）: リポジトリ直下 [`NOTICE`](../NOTICE)

---

## アイコン方針（決定案）

| 項目 | 方針 |
|------|------|
| ライブラリ | **Heroicons v2** に基本統一 |
| スタイル | **outline**（`home.blade.php` の既存インライン SVG と揃える） |
| 実装 | 共通 Blade コンポーネント + インライン SVG（npm 必須ではない） |
| 表記 | ルート `NOTICE` に Heroicons の MIT 表示を記載済み |

---

## サマリー

| 項目 | 値 |
|------|-----|
| 絵文字を含む Blade ファイル数 | **30** / 58 |
| 絵文字・記号の出現回数（概算） | **106** 箇所 |
| ユニークな絵文字・記号の種類 | **26** 種（⏳ 含む） |
| 既存のモダンアイコン（インライン SVG） | 多数の画面で併用済み（例: `home.blade.php` のカード、`layouts` 周辺のスピナー） |

**補足:** ナビゲーション用の矢印文字（`←` `→`）は本調査では「絵文字置換対象」から除外している。ユーザー要望の ✏️・🏠・🗑️ 系に近いのは、主に **✏️ 🗑️ 📋 🏷️** など。

**🏠 は Blade 内では未使用**（ホーム画面のカードはすでに SVG アイコン）。

---

## 絵文字一覧（意味別カテゴリ）

### 1. CRUD・アクション系（置換優先度: 高）

| 絵文字 | 想定意味 | 主な使用箇所 |
|--------|----------|--------------|
| ✏️ | 編集 | 詳細画面ヘッダー（column, stressor, problem-solving, writing-disclosure） |
| 🗑️ | 削除 | 同上 + 編集フォーム内の行削除・モーダル確認 |
| 📋 | コピー / クリップボード | トースト、ボタンラベル、空状態 |
| ✨ | 保存・更新（強調） | フォーム送信ボタン |
| ✓ | 完了・転記済み | ステータスバッジ |

### 2. ラベル・セクション見出し系（優先度: 中）

| 絵文字 | 想定意味 | 主な使用箇所 |
|--------|----------|--------------|
| 🏷️ | タグ | コラム・ストレッサー・問題解決のタグ欄 |
| 📊 | 改善レベル | 問題解決・実行計画一覧 |
| 📝 | メモ・実行計画・コラム関連 | ラベル、空状態、保存ボタン |
| 📜 | 年表・件数表示 | スキーマ療法 年表 |
| 💬 | 対話ワーク | スキーマ療法 対話系画面 |
| 💭 | 思考・振り返り | 筆記開示、適応的思考、実行計画 |
| 🎵 | 音の種類 | マインドフルネス |
| 🔍 | 件数・検索系表示 | 対話ワーク一覧 |
| 🔧 | 管理画面 | admin/menu |
| 💜 | コーピング管理 | admin/coping/menu |

### 3. 感情・極性表示（優先度: 中〜要検討）

| 絵文字 | 想定意味 | 主な使用箇所 |
|--------|----------|--------------|
| 😊 | ポジティブ | コラム、ストレッサー、年表編集 |
| 😔 | ネガティブ | コラム、ストレッサー |
| 😢 | データなし（空状態） | 多数の一覧・詳細 |
| ⚠️ | 警告バナー | home（振り返り未実施） |

### 4. 空状態・装飾・その他（優先度: 低〜中）

| 絵文字 | 想定意味 | 主な使用箇所 |
|--------|----------|--------------|
| 🎉 / ⏳ | フィルタ結果（完了 / 待ち） | problem-solving-plans-list（Alpine `x-text`） |
| 🌱 | コーピング空状態 | copings |
| 🧩 | 問題解決一覧空状態 | problem-solvings-list |
| ⚡ | ストレッサー一覧空状態 | stressor-and-responses-list |
| 👥 | サポートネットワーク空状態 | support-networks |
| 🚧 | 工事中（管理タグ画面） | admin/coping/tag |

---

## 絵文字 → Heroicons 差し替え対応表（推奨）

いずれも **Heroicons v2 / outline / 24×24**。名称は [heroicons.com](https://heroicons.com) のスラッグ。

### 操作・ステータス（優先度: 高）

| 現在 | 推奨 Heroicons | 代替案 | 用途・備考 |
|------|----------------|--------|------------|
| ✏️ | `pencil-square` | `pencil` | 編集リンク・ボタン。タップ領域がある操作は square 推奨 |
| 🗑️ | `trash` | — | 削除。危険操作は `text-red-500` 等で色分け |
| 📋 | `clipboard-document` | `clipboard-document-check` | コピー操作・「内容をコピー」。完了トーストは **check** 版も可 |
| ✨ | `arrow-down-tray` | `check`, `sparkles` | 保存・更新。**意味を明確にするなら arrow-down-tray または check**。装飾のみなら sparkles |
| ✓ | `check` | `check-circle`, `check-badge` | 転記済み・振り返り済みバッジ。強調するなら circle / badge |

### ラベル・セクション（優先度: 中）

| 現在 | 推奨 Heroicons | 代替案 | 用途・備考 |
|------|----------------|--------|------------|
| 🏷️ | `tag` | `hashtag` | タグ入力・見出し |
| 📊 | `chart-bar` | `presentation-chart-bar` | 改善レベル。一覧の数値強調なら chart-bar で十分 |
| 📝 | `document-text` | `pencil-square` | メモ・実行計画・コラム。編集意味が強い場合は pencil-square |
| 📜 | `queue-list` | `document-text`, `newspaper` | 年表・件数。時系列なら queue-list が意味に近い |
| 💬 | `chat-bubble-left-right` | `chat-bubble-left` | 対話ワーク |
| 💭 | `chat-bubble-bottom-center-text` | `light-bulb` | 思考・振り返り・筆記開示の説明。思考文脈は chat 系、ひらめきなら light-bulb |
| 🎵 | `musical-note` | `speaker-wave` | マインドフルネス音選択。再生中強調なら speaker-wave |
| 🔍 | `magnifying-glass` | `queue-list` | 検索結果なし・「全 N 件」。**件数表示のみ**なら `queue-list` の方が意味的に正確な場合あり |
| 🔧 | `wrench-screwdriver` | `cog-6-tooth` | 管理画面タイトル |
| 💜 | `heart` | — | コーピング管理。色は `text-purple-600` 等で表現（アイコン形状は heart） |

### 感情・極性（優先度: 中）

| 現在 | 推奨 Heroicons | 代替案 | 用途・備考 |
|------|----------------|--------|------------|
| 😊 | `face-smile` | `hand-thumb-up` | ポジティブ感情ラベル |
| 😔 | `face-frown` | `hand-thumb-down` | ネガティブ感情ラベル |
| 😢 | `inbox` | `document`, `folder-open` | **データなし空状態**。感情表現ではなく「未登録」なので顔アイコンは非推奨 |
| ⚠️ | `exclamation-triangle` | `exclamation-circle` | 警告バナー（home 振り返り未実施） |

### 空状態・その他（優先度: 低〜中）

| 現在 | 推奨 Heroicons | 代替案 | 用途・備考 |
|------|----------------|--------|------------|
| 🎉 | `check-circle` | `trophy`, `sparkles` | フィルタ「完了」側（Alpine 動的表示） |
| ⏳ | `clock` | `arrow-path` | フィルタ「待ち」側。待機なら clock |
| 🌱 | `sun` | `sparkles`, `arrow-trending-up` | コーピング空状態。成長・ウェルネスなら sun |
| 🧩 | `puzzle-piece` | — | 問題解決一覧空状態（home のスキーマ療法カードと統一可） |
| ⚡ | `bolt` | — | ストレッサー一覧空状態（home カードと同形で統一可） |
| 👥 | `user-group` | `users` | サポートネットワーク空状態 |
| 🚧 | `wrench-screwdriver` | `exclamation-triangle` | 工事中プレースホルダー |

### 記号（Unicode、絵文字ではないが置換対象）

| 現在 | 推奨 Heroicons | 備考 |
|------|----------------|------|
| ✓（U+2713） | `check` | 上表の ✓ と同一コンポーネントに統合 |

### 置換しないもの（参考）

| 文字 | 理由 |
|------|------|
| `←` `→` | ナビゲーション用テキスト。必要なら `arrow-left` / `arrow-right` に段階的に変更可 |

### Alpine.js 動的表示の注意

`problem-solving-plans-list.blade.php` L185 の `x-text="'🎉' : '⏳'"` は、次のいずれかに変更する。

1. `<template x-if="filter === 'completed'"><x-icon name="check-circle" /></template>` のように DOM 分岐
2. 共通コンポーネントに `icon` プロパティを渡す Alpine ヘルパー

`x-text` のままでは Heroicons SVG を表示できない。

### 実装時のサイズ目安

| 文脈 | Tailwind クラス例 |
|------|-------------------|
| インラインラベル | `w-4 h-4` |
| ツールバー（編集・削除） | `w-5 h-5` |
| ボタン内 | `w-5 h-5` + `gap-2` |
| 空状態（大） | `w-16 h-16` `text-gray-300` |
| ページタイトル横 | `w-7 h-7` |

### path の取得例

公式リポジトリ: `https://github.com/tailwindlabs/heroicons/blob/master/src/24/outline/{name}.svg`

例: `pencil-square` → `src/24/outline/pencil-square.svg` の `<path>` をコンポーネントに取り込む。

---

## ファイル別詳細

### 管理画面（3 ファイル）

#### `resources/views/admin/menu.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 8 | 🔧 | ページタイトル「管理画面」 |

#### `resources/views/admin/coping/menu.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 8 | 💜 | ページタイトル「コーピングリスト管理」 |

#### `resources/views/admin/coping/tag.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 8 | 🏷️ | ページタイトル |
| 11 | 🚧 | プレースホルダー（工事中表示） |

---

### トップ・共通

#### `resources/views/home.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 13 | ⚠️ | 振り返り未実施の警告バナー |

※ 機能カードは **SVG** で実装済み（絵文字なし）。

---

### 認知再構成・コラム（5 ファイル）

#### `resources/views/column-detail.blade.php`（8 箇所）

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 19 | 📋 | コピー完了トースト |
| 46 | ✏️ | 編集リンク |
| 53 | 🗑️ | 削除ボタン |
| 72 | 🏷️ | 「タグ」ラベル |
| 124 | ✨ | 「適応的思考」見出し装飾 |
| 141 | 📝 | 適応的思考バッジ内 |
| 153 | 📋 | 「内容をコピー」ボタン |
| 160 | 😢 | 404 / 未読込時の空表示 |

#### `resources/views/columns.blade.php`（12 箇所）

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 92 | ✓ | 転記済みバッジ |
| 224 | 📝 | UI ラベル（span） |
| 248 | 📋 | UI ラベル（span） |
| 332 | 🏷️ | タグラベル |
| 385, 522 | 😔 | ネガティブ感情ラベル |
| 400, 537 | 😊 | ポジティブ感情ラベル |
| 564 | 📝 | 番号バッジ内 |
| 590, 593 | ✨ | 保存・更新ボタン |
| 612 | 📋 | 「入力内容をコピー」 |

#### `resources/views/columns-list.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 120 | 📝 | 一覧が空のときの大きな表示 |

#### `resources/views/column-adaptive-thoughts-list.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 54 | 💭 | 空状態 |

---

### ストレッサーとストレス反応（3 ファイル）

#### `resources/views/stressor-and-response-detail.blade.php`（6 箇所）

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 19 | 📋 | コピートースト |
| 46 | ✏️ | 編集 |
| 53 | 🗑️ | 削除 |
| 71 | 🏷️ | タグ |
| 127 | 📋 | コピーボタン |
| 134 | 😢 | 空状態 |

#### `resources/views/stressor-and-responses.blade.php`（7 箇所）

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 35 | 📋 | ラベル span |
| 118 | 🏷️ | タグ |
| 192 | 😔 | ネガティブ |
| 207 | 😊 | ポジティブ |
| 276, 279 | ✨ | 保存・更新 |
| 298 | 📋 | コピー |

#### `resources/views/stressor-and-responses-list.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 130 | ⚡ | 空状態 |

---

### 問題解決（5 ファイル）

#### `resources/views/problem-solving-detail.blade.php`（7 箇所）

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 19 | 📋 | コピートースト |
| 34 | ✏️ | 編集 |
| 41 | 🗑️ | 削除 |
| 71 | 🏷️ | タグ |
| 146 | ✓ | 振り返り済み |
| 191 | 📊 | 改善レベル表示 |
| 209 | 📋 | コピー |

#### `resources/views/problem-solving-edit.blade.php`（8 箇所）

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 54 | 📋 | ラベル |
| 129, 249, 407 | 🗑️ | 削除（ヘッダー・行・計画） |
| 172 | 🏷️ | タグ |
| 384 | ✓ | 振り返り済み |
| 463 | 📊 | 改善レベル |
| 531 | 📋 | コピー |

#### `resources/views/problem-solving-plans-list.blade.php`（10 箇所）

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 24 | 📊 | フィルタラベル |
| 113 | ✓ | 振り返り済み |
| 127 | 📋 | 問題状況ラベル |
| 133 | 📝 | 実行計画ラベル |
| 140 | 💭 | 振り返りラベル |
| 150 | 📊 | 改善 Lv. |
| 176 | 📋 | 空状態（大） |
| 185 | 🎉 / ⏳ | Alpine 動的（完了 / 待ち） |
| 191 | 🔍 | 検索結果なし空状態 |

#### `resources/views/problem-solvings-list.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 118 | 🧩 | 空状態 |

---

### 筆記開示・メモ（4 ファイル）

#### `resources/views/writing-disclosure-detail.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 19 | ✏️ | 編集 |
| 26 | 🗑️ | 削除 |
| 38 | 😢 | 空状態 |

#### `resources/views/writing-disclosures.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 27 | 💭 | 説明文先頭 |
| 58 | 📝 | 送信ボタン「書き出す」 |
| 59 | ✨ | 送信ボタン「更新する」 |

#### `resources/views/writing-disclosures-list.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 28 | 📝 | 空状態 |

#### `resources/views/simple-notepads.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 76 | 🗑️ | 削除 |
| 167 | 📝 | 「メモを保存」ボタン |

#### `resources/views/simple-notepads-list.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 38 | 📝 | 空状態 |

---

### スキーマ療法（6 ファイル）

#### `resources/views/schema-therapy-chronology.blade.php`（5 箇所）

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 10 | 📜 | 件数表示 |
| 50, 56 | 😊 / 😢 | ポジティブ / ネガティブ |
| 95, 107 | 😢 / 📜 | 空状態 |

#### `resources/views/schema-therapy-chronology-edit.blade.php`（6 箇所）

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 18 | 🗑️ | 削除ボタン |
| 158, 168 | 😊 / 😢 | 極性ラベル |
| 184, 187 | ✨ | 保存・更新 |
| 228 | 🗑️ | 削除確認モーダル |

#### `resources/views/schema-therapy-dialogue-work-list.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 14 | 🔍 | 件数 |
| 48 | 😢 | 空状態 |
| 60 | 💬 | 空状態 |

#### `resources/views/schema-therapy-dialogue-work-edit.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 20 | 🗑️ | 削除 |
| 140 | 💬 | 空状態 |
| 214 | 🗑️ | 削除確認 |

#### `resources/views/schema-therapy-mode-work-dialogue-list.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 14 | 🔍 | 件数 |
| 53 | 😢 | 空状態 |
| 65 | 💬 | 空状態 |

#### `resources/views/schema-therapy-mode-work-dialogue-edit.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 20 | 🗑️ | 削除 |
| 116 | 💬 | CTA「対話のワークを始める」 |
| 193 | 💬 | 空状態 |
| 267 | 🗑️ | 削除確認 |

---

### その他機能（4 ファイル）

#### `resources/views/early-maladaptive-schemas.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 745 | 📋 | 「備考欄」見出し |

#### `resources/views/mindfulness.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 27 | 🎵 | 音の種類ラベル |

#### `resources/views/copings.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 172 | 🌱 | 空状態 |

#### `resources/views/support-networks.blade.php`

| 行 | 絵文字 | コンテキスト |
|----|--------|--------------|
| 112 | 👥 | 空状態 |

---

## 絵文字を含まない Blade（参考）

以下 28 ファイルは今回のスキャンで **絵文字・記号アイコンは検出されず**（vendor/mail、auth、schema-therapy 本体の一部、layouts など）:

- `resources/views/layouts/app.blade.php`
- `resources/views/components/feature-card.blade.php`
- `resources/views/components/pagination.blade.php`
- `resources/views/auth/*.blade.php`
- `resources/views/schema-therapy.blade.php`
- `resources/views/schema-therapy-mode-work.blade.php`
- `resources/views/schema-therapy-mode-work-dialogue.blade.php`
- `resources/views/schema-therapy-mode-map.blade.php`
- `resources/views/vendor/**`
- など

---

## 重複パターン（置換時の DRY 候補）

同じ絵文字が複数ファイルで繰り返されている。`<x-icon name="..." />` にまとめると、上表の Heroicons 名を一箇所で管理できる。

| パターン | 出現ファイル数（概算） | コンポーネント名（案） |
|----------|------------------------|-------------------------|
| ✏️ 編集 | 4+ | `pencil-square` |
| 🗑️ 削除 | 10+ | `trash` |
| 📋 コピー | 10+ | `clipboard-document` |
| 🏷️ タグ | 5+ | `tag` |
| ✨ 保存/更新 | 6+ | `arrow-down-tray` または `check` |
| 😢 空状態 | 8+ | `inbox` |
| 😊 / 😔 極性 | 6+ | `face-smile` / `face-frown` |
| 📝 メモ系 | 6+ | `document-text` |
| 📊 改善レベル | 4+ | `chart-bar` |

---

## 技術スタック上のメモ（次フェーズ用）

- **ライセンス:** ルート [`NOTICE`](../NOTICE) に Heroicons MIT を記載。方針は [`THIRD_PARTY_NOTICES.md`](THIRD_PARTY_NOTICES.md)。
- **npm 依存:** 必須ではない。path を Blade コンポーネントに保持する方式で可（現状のインライン SVG を DRY 化）。
- **既存パターン:** `home.blade.php` のカード・`layouts/app.blade.php` の home アイコンは既に Heroicons outline 系 path。新規置換も同じ stroke ルールに揃える。
- **Alpine.js 動的絵文字:** `problem-solving-plans-list.blade.php` L185 — 上表「Alpine.js 動的表示の注意」参照。
- **Variation Selector:** ソース上は `✏️` `🗑️` `🏷️` のように表示されるが、一部はベース文字 + U+FE0F の組み合わせ。

---

## 推奨する次のステップ

1. ~~アイコン方針の決定~~ → **Heroicons v2 outline に統一**（本ドキュメント・`NOTICE` 参照）
2. 共通 Blade コンポーネント `resources/views/components/icon.blade.php` を作成（name → SVG path マップ）
3. 優先度「高」から置換: ✏️ → `pencil-square`、🗑️ → `trash`、📋 → `clipboard-document`
4. 空状態の 😢 → `inbox`（顔文字アイコンは使わない）
5. 管理画面タイトル: 🔧 → `wrench-screwdriver`、💜 → `heart`、🏷️ → `tag`
6. `home.blade.php` L13 の ⚠️ → `exclamation-triangle`（他カードと同じく SVG 化）

---

## 調査コマンド（再現用）

```bash
python3 << 'PY'
from pathlib import Path
root = Path("resources/views")
for path in sorted(root.rglob("*.blade.php")):
    for i, line in enumerate(path.read_text(encoding="utf-8").splitlines(), 1):
        for ch in line:
            o = ord(ch)
            if (0x1F300 <= o <= 0x1FAFF or 0x2600 <= o <= 0x27BF or
                0x2300 <= o <= 0x23FF or ch in "✏✓") and ch not in "←→":
                print(f"{path}:{i}: {ch!r} {line.strip()[:80]}")
                break
PY
```

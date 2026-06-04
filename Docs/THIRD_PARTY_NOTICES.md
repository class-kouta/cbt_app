# サードパーティ表記（日本語要約）

本アプリケーションの UI アイコンは、原則として **[Heroicons](https://heroicons.com)**（Tailwind Labs, Inc.、**MIT License**）に統一する。

法的な全文（英語）はリポジトリ直下の [`NOTICE`](../NOTICE) を参照すること。

---

## Heroicons 利用方針

| 項目 | 内容 |
|------|------|
| バージョン | Heroicons **v2** |
| スタイル | **outline**（既存の `home.blade.php` 等のインライン SVG と同系） |
| サイズ | `viewBox="0 0 24 24"`、表示は Tailwind（例: `w-5 h-5`） |
| 実装 | 共通 Blade コンポーネント（例: `<x-icon name="pencil-square" />`）から path を出力する想定 |
| 取得元 | [heroicons.com](https://heroicons.com) または [GitHub](https://github.com/tailwindlabs/heroicons) の SVG path |

### 著作権表示の置き場所

- **配布物・リポジトリ:** ルートの `NOTICE`（MIT 全文 + 利用範囲の説明）
- **開発者向け:** 本ファイル + `Docs/BLADE_EMOJI_AUDIT.md`（絵文字→アイコン対応表）

### 注意

- path を Blade にコピーして使う場合も、MIT の条件（著作権表示の保持）が適用される。
- Heroicons 以外の SVG を混在させる場合は、本ファイルと `NOTICE` に出典を追記する。

---

## MIT License（Heroicons）— 要約（非公式）

- 商用・改変・再配布が可能
- 条件: 著作権表示と MIT ライセンス文を配布物に含める
- 保証なし

正式な条文は [`NOTICE`](../NOTICE) の英語全文に従う。

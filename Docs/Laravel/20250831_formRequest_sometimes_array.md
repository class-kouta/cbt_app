# FormRequestで空配列を許容しつつ、要素がある時だけ検証する方法（sometimes）

## TL;DR
- 配列フィールドが「省略可」で「空配列OK」、かつ「要素がある時だけ要素検証したい」場合は、親に `sometimes|array` を使う。
- 子要素の検証は `field.*` に定義（配列が空なら子ルールは実行されない）。
- 取得側は `($validated['field'] ?? [])` で空配列に正規化して扱う。

## 使い所
- 任意のタグ一覧、任意のIDリストなど「送られないこともある」「空配列＝選択なし」とみなしたいケース。

## 具体例（`CreateCopingRequest`）

```php
// app/Http/Requests/Coping/CreateCopingRequest.php（抜粋）
return [
    'difficulty_id' => ['required', 'integer', 'min:1'],
    'content' => ['required', 'string', 'max:10000'],
    'tag_ids' => ['sometimes', 'array'],
    'tag_ids.*' => ['integer', 'min:1', 'distinct'],
];
```

コントローラ/ユースケース側での受け取り例:

```php
$validated = $request->validated();
$tagIds = $validated['tag_ids'] ?? []; // キー未送信でも空配列として扱える
// 例: $todo->tags()->sync($tagIds);
```

## ルールの意味と比較
- sometimes: フィールドが「送られてきた場合にのみ」他のルールを適用。未送信なら検証スキップ。
- array: 値が配列であることを保証（nullや文字列はNG、空配列はOK）。
- present: キーの「存在」を必須にする（空でもOK）。
- filled: キーがあるなら「空NG」（空配列/空文字/nullをNG）。
- required: そもそも必須＋空NG。
- nullable: 値がnullでもOK（nullなら他ルールは基本スキップ）。

今回の方針（「キー省略＝タグ無し」）には `sometimes|array` が最適。

## 挙動イメージ
- 未送信（キー無し）: 親の検証スキップ → `$validated['tag_ids'] ?? []` で空配列として扱う。
- `tag_ids: []`（空配列）: 親の `array` はOK、子 `tag_ids.*` は要素が無いので実行されない。
- `tag_ids: null`: 親が `array` に違反 → 422。
- `tag_ids: [1, 1]`: 子の `distinct` に違反 → 422。
- `tag_ids: ['2']`: PHPの型緩和により `integer` が通ることがある。厳密化したい場合は運用方針/型変換を検討。

## よくある強化オプション
- `exists:tags,id`: 早期に外部キー存在チェック。
- `max:20`: タグ上限。
- 空配列を禁止したい場合: 親に `min:1` を追加（例: `['sometimes', 'array', 'min:1']`）。
- キーを必ず送ってほしい場合: 親を `['present','array']` に変更（今回の「キー省略OK」方針とは相反）。

## まとめ
- 「空配列＝選択なし」「キー省略もOK」なら、`sometimes|array` が最も自然で保守的。
- 子要素のバリデーションは `field.*` に委ね、空配列時は自動的にスキップされる。
- 実際の利用時は `($validated['field'] ?? [])` で空配列に正規化して処理する。

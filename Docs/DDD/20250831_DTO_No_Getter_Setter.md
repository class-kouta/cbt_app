# DTOにはゲッターもセッターも不要（方針）

本ドキュメントは、アプリケーション層のDTO（Data Transfer Object）に関する設計方針を示します。

## 結論
- DTOはデータ運搬のためのオブジェクト。ビジネスロジックや表現ロジックは持たない。
- PHP 8.2 以上では「readonly class + public 型付きプロパティ」を採用し、ゲッター/セッターは定義しない。
- 不変性（immutability）はクラスレベルの`readonly`で担保する。

## 背景と理由
- **責務の明確化**: DTOは層間のデータ受け渡し専用。計算・整形・権限判定などは他レイヤー（ユースケース、Presenter/Resource、ValueObject）に置く。
- **シンプルさ/可読性**: プロパティに直接アクセスできる方が、読みやすく、意図が明確。
- **保守性**: 不要なメソッドを増やさず、将来的な仕様変更の影響範囲を最小化。
- **パフォーマンス**: アクセサ層が薄く、シリアライズ/デシリアライズも単純。

## 実装ルール
1. PHP 8.2+ の `readonly class` を使用する。
2. すべてのプロパティは `public` かつ型を必須とする。
3. 配列プロパティは要素型を `@var` で明記する（例: `@var int[]`）。
4. バリデーションはHTTP層（FormRequestなど）で行い、DTO内には置かない。
5. 変換/整形/集計などのロジックはDTOに置かない。
6. 生成はコンストラクタに集約し、副作用を持たない。

## サンプル
アプリケーション層の`CopingData`は以下方針に基づく実装とする。

```php
namespace App\Application\DTO;

readonly class CopingData
{
    public function __construct(
        public string $content,
        public ?int $point,
        /** @var int[] */
        public array $tagIds = []
    ) {
    }
}
```

## 例外と判断基準
- 取得時に非自明な変換・権限マスキング・遅延計算等が必要なら、DTOではなく以下のいずれかに移す:
  - ValueObject（意味と制約を持つ小さな型）
  - Presenter/Resource（出力整形の責務）
  - UseCaseやDomain Service（計算や集計責務）
- セッターは使用しない。不変条件を崩す必要がある場合は、新しいDTOを生成して差し替える（コマンド/クエリ分離の観点）。

## よくある質問
- Q. Eloquentとのマッピングは？
  - A. HTTP層でバリデーション後、コントローラやUseCaseで配列→DTOを生成し、リポジトリ層で必要に応じてエンティティへ変換する。
- Q. 後から取得ロジックを差し込みたい場合は？
  - A. DTOにメソッドを増やさず、ValueObjectやPresenterへロジックを移す（DTOは薄いまま保つ）。

## まとめ
- DTOは「薄く・不変・型付き・直アクセス」。ゲッター/セッターは不要。
- 表現や計算の知識は、DTO以外の適切な層/型に配置する。

# Laravelプロトタイプ開発 実装ステップ

`DEVELOPMENT_PLAN.md` のステップ2「Laravel + bladeでプロトタイプを作成」を具体化したものです。TDD/DDDのアプローチを前提とし、機能単位でタスクを定義しています。

**実装方針**: 機能を優先して実装し、動作確認後にファイル移行を行うことで、手戻りを最小限に抑えます。

---



### フェーズ 1: TODOの基本機能 (CRUD)

2.  **TODO作成**
    *   **内容:** 新しいTODOを登録するAPIと、入力フォームを持つBladeビューを作成します。ドメイン層のエンティティ、アプリケーション層のユースケース、プレゼンテーション層のコントローラー、インフラストラクチャ層のリポジトリ実装を新規作成します。

3.  **TODO一覧表示**
    *   **内容:** 未完了TODOを一覧表示するAPIと、それを表示するBladeビューを作成します。

4.  **TODO更新**
    *   **内容:** 既存TODOの内容を編集するAPIと、編集フォームを持つBladeビューを作成します。

5.  **TODO完了**
    *   **内容:** TODOを完了状態にするAPIと、完了ボタンをBladeビューに実装します。

6.  **TODO削除**
    *   **内容:** TODOを削除するAPIと、削除ボタンをBladeビューに実装します。

### フェーズ 2: 難易度とタグの管理

7.  **難易度マスタの登録 (Seeder)**
    *   **内容:** `difficulties`テーブルに初期データ（小・中・大）を投入するSeederを作成・実行します。
    *   **注意:** シーダーファイルはデフォルトの`database/seeders/`に作成し、後でインフラストラクチャ層に移行します。

8. **タグ作成**
    *   **内容:** 新しいタグを作成するAPIと、入力フォームを持つBladeビューを作成します。

9. **タグ一覧表示**
    *   **内容:** ユーザーが作成したタグを一覧表示するAPIとBladeビューを作成します。

10. **タグ削除**
    *   **内容:** タグを削除するAPIと、削除ボタンをBladeビューに実装します。

11. **TODO作成・更新機能への紐付け**
    *   **内容:** TODOの作成・更新時に、難易度とタグを紐付けられるようにAPIとBladeビューを改修します。

### フェーズ 3: 達成感を味わうための機能

12. **完了TODO一覧表示**
    *   **内容:** 完了済みのTODOを一覧表示する専用のAPIとBladeビューを作成します。

13. **完了TODOの絞り込み**
    *   **内容:** 完了TODO一覧を「年月」や「タグ」で絞り込むAPIと、絞り込みUIをBladeビューに実装します。

14. **完了TODOの統計表示**
    *   **内容:** 指定期間の完了タスク数や難易度別件数を集計するAPIと、その結果を表示するBladeビューを作成します。

### フェーズ 4: DDDディレクトリ構造への移行

15. **モデルファイルとmigrationファイルの移行**
    *   **内容:**
        - Eloquentモデルファイル（`app/Models/`内）を`app/Infrastructure/Database/Models/`に移動
        - マイグレーションファイル（`database/migrations/`内）を`app/Infrastructure/Database/Migrations/`に移動
        - ファクトリーファイル（`database/factories/`内）を`app/Infrastructure/Database/Factories/`に移動
        - シーダーファイル（`database/seeders/`内）を`app/Infrastructure/Database/Seeders/`に移動
        - `composer.json`のオートロード設定、`config/database.php`のマイグレーションパス、各種サービスプロバイダーの設定を更新
    *   **目的:** 完全なDDDディレクトリ構造を完成させ、設計上の一貫性を確保します。

16. **最終動作検証とリファクタリング**
    *   **内容:** 全機能の動作確認、テストの実行、コードの最適化を行います。
    *   **目的:** プロトタイプの品質を確保し、次の開発フェーズへの準備を整えます。

---

### 補足: データベース関連ファイルの配置について

- **マイグレーションファイル**: 当面デフォルトの`database/migrations/`に配置して運用し、フェーズ4（DDDディレクトリ構造への移行）開始時に`app/Infrastructure/Database/Migrations/`へ移行します。
- **モデルファイル (Eloquent Models)**: 当面デフォルトの`app/Models/`に配置し、フェーズ4開始時に`app/Infrastructure/Database/Models/`へ移行します。
- **ファクトリーファイル**: 当面デフォルトの`database/factories/`に配置し、フェーズ4開始時に`app/Infrastructure/Database/Factories/`へ移行します。
- **シーダーファイル**: 当面デフォルトの`database/seeders/`に配置し、フェーズ4開始時に`app/Infrastructure/Database/Seeders/`へ移行します。

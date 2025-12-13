# 問題解決法機能 要件定義書

## 概要

認知行動療法（CBT）の技法の一つである「問題解決法」を試せる機能を作成します。

**問題解決ワークシートの目的:**
対処可能な課題を設定し、行動実験をしてみよう

問題解決法は、日常で直面する問題に対してステップを踏んで解決策を見つけていく技法です。「悩む」のではなく「何らかの解決を試みるべき状況」として捉え、具体的なアクションに落とし込めるのが特徴です。

---

## 機能の目的

- ストレスや悩みの原因となる「問題」を具体的に把握する
- 問題解決に向けた考え方を整える
- 問題が解決・改善された状況を具体的にイメージする
- 複数の解決策を案出し、効果と実行可能性を検討する
- 行動実験として具体的な実行計画を立てる
- 実行結果を振り返り、成功体験を積み重ねる（達成感）

---

## ユビキタス言語

| 用語 | 定義 |
|------|------|
| **問題解決法（Problem Solving）** | 対処可能な課題を設定し、行動実験として解決を試みるアプローチ |
| **問題状況（Problem Situation）** | 解決したい困りごとや悩みの具体的な状況 |
| **考えをととのえる（Mindset Preparation）** | 問題解決に向けた心構えを整えること |
| **自分への声かけ（Self-Talk）** | 問題解決に向けて自分に言う励ましの言葉 |
| **改善イメージ（Improved Image）** | 問題が解決・改善された状況の具体的なイメージ |
| **解決策（Solution）** | 問題を解決するためのアイデアや方法 |
| **効果的か（Effectiveness）** | 解決策がどの程度効果的かの評価（0-100%） |
| **実行可能か（Feasibility）** | 解決策がどの程度実行可能かの評価（0-100%） |
| **行動実験（Behavioral Experiment）** | 解決策を「実験」として試してみること |
| **実行計画（Action Plan）** | 行動実験のための具体的な計画 |
| **振り返り（Reflection）** | 行動実験を試した後の結果や気づき |

---

## ステップ構成（6ステップ）

### Step 1: 問題状況を具体的に把握する

**説明:** 自分、人間関係、出来事、状況、その他の観点から問題を整理する

**記録する項目:**
- **問題状況**（何が起きているか、具体的に記述）※必須

### Step 2: 問題解決に向けて、自分の考えをととのえる

**説明:** 問題に向き合うための心構えを整える

**チェックリスト（画面表示のみ、DBには保存しない）:**
- □ 生きていれば、何らかの問題は生じるものだ。問題があること自体を受け入れよう。
- □ 原因を1つに決めつけず、さまざまな要因を見つけてみよう。
- □ 問題を「悩む」のではなく、「何らかの解決を試みるべき状況」ととらえてみよう。
- □ 大きな問題は小分けにしてみよう。小さな問題に分解して、突破口を見つけよう。
- □ 「解決できるか」ではなく、「対処できそうなこと」「できないこと」を見極めよう。
- □ できることから手をつけよう。「実験」としてチャレンジしてみよう。
- □ どんなことを自分に言うと、良いだろうか？

**記録する項目:**
- **自分への声かけ**（自分に言う励ましの言葉）※任意

### Step 3: 問題状況が解決または改善された状況を具体的にイメージする

**説明:** 問題が解決したらどうなっているか、具体的にイメージする

**記録する項目:**
- **改善イメージ**（解決・改善された状況の具体的なイメージ）※任意

### Step 4: 問題の解決・改善のための具体的な手段を案出し、検討する

**説明:** 解決策を複数出し、それぞれの効果と実行可能性を評価する

**記録する項目:**
- **解決策リスト**（最大7つ）※任意
  - 解決策の内容
  - 効果的か（0-100%）
  - 実行可能か（0-100%）

### Step 5: 行動実験のための具体的な実行計画を立てる

**説明:** 選んだ解決策を「行動実験」として実行するための計画を立てる

**計画に盛り込むポイント:**
- いつ
- どこで
- どんなとき
- 誰と・誰に対して
- 何をどうする
- 実行を妨げる要因とその対策は
- 結果の検証の仕方

**記録する項目:**
- **実行計画**（上記ポイントを含む具体的な計画）※任意

### Step 6: 振り返り

**説明:** 行動実験を実行した後の結果を振り返る

**記録する項目:**
- **振り返り**（実行結果、うまくいったこと、改善点、学んだこと、次に活かせること等を自由記述）※任意

---

## データベース設計

### problem_solvings テーブル（メインテーブル）

| カラム名 | 型 | 制約 | 説明 |
|---------|-----|------|------|
| id | bigint | 主キー | 一意識別子 |
| problem_situation | text | NOT NULL | 問題状況（Step 1）※必須 |
| self_talk | text | NULL可 | 自分への声かけ（Step 2） |
| improved_image | text | NULL可 | 改善イメージ（Step 3） |
| action_plan | text | NULL可 | 実行計画（Step 5） |
| reflection | text | NULL可 | 振り返り（Step 6） |
| created_at | timestamp | - | 作成日時 |
| updated_at | timestamp | - | 更新日時 |

**補足:**
- `mindset_check`（考えをととのえるチェックリスト）はDBに保存しない（画面表示のみ）
- 下書き機能（status）は不要

### problem_solving_solutions テーブル（解決策）

| カラム名 | 型 | 制約 | 説明 |
|---------|-----|------|------|
| id | bigint | 主キー | 一意識別子 |
| problem_solving_id | bigint | 外部キー → problem_solvings.id | 問題解決ID |
| content | text | NOT NULL | 解決策の内容 |
| effectiveness | tinyint | NULL可 | 効果的か（0-100%） |
| feasibility | tinyint | NULL可 | 実行可能か（0-100%） |
| sort_order | integer | NOT NULL | 表示順（1-7） |
| created_at | timestamp | - | 作成日時 |
| updated_at | timestamp | - | 更新日時 |

---

## ドメインモデル

### エンティティ

#### ProblemSolving（問題解決）

**説明**: 問題解決法の記録を表すエンティティ

**属性:**
- ID（一意識別子）
- 問題状況 ※必須
- 自分への声かけ
- 改善イメージ
- 実行計画
- 振り返り
- 解決策（複数、最大7つ）
- 作成日時
- 更新日時

**振る舞い:**
- 作成
- 更新
- 閲覧
- 削除

#### ProblemSolvingSolution（解決策）

**説明**: 問題解決法で案出した解決策を表すエンティティ

**属性:**
- ID（一意識別子）
- 問題解決ID
- 内容
- 効果的か（0-100%）
- 実行可能か（0-100%）
- 表示順（1-7）
- 作成日時
- 更新日時

**振る舞い:**
- 作成
- 評価更新
- 削除

---

## 画面設計

### 1. 問題解決法一覧画面

- 作成した問題解決の一覧表示
- 各カードに以下を表示:
  - 作成日時
  - 問題状況（2行まで表示、それ以上は省略）
  - **未入力項目タグ**（コラム法と同様のUI）
    - 例: `未入力: 声かけ` `未入力: 改善イメージ` `未入力: 解決策` `未入力: 実行計画` `未入力: 振り返り`
- 新規作成ボタン（フローティング）

**未入力判定対象:**
| フィールド | 表示名 |
|-----------|--------|
| self_talk | 声かけ |
| improved_image | 改善イメージ |
| solutions（0件） | 解決策 |
| action_plan | 実行計画 |
| reflection | 振り返り |

### 2. 問題解決作成/編集画面

- **Step 1〜6をアコーディオンまたはセクションで表示**
- Step 2のチェックリストはチェックボックスで表示（画面表示のみ、保存しない）
- Step 4の解決策リストは表形式（最大7行、動的に追加可能）
- 各項目は任意入力（problem_situationのみ必須）
- 後からいつでも編集可能

### 3. 問題解決詳細画面

- 記録した全ステップの内容を一覧表示
- 未入力項目は「未入力」とグレーで表示（コラム法と同様）
- 編集ボタン
- 削除ボタン

---

## API エンドポイント設計

| メソッド | エンドポイント | 説明 |
|---------|---------------|------|
| GET | `/api/problem-solvings` | 一覧取得 |
| POST | `/api/problem-solvings` | 新規作成 |
| GET | `/api/problem-solvings/{id}` | 詳細取得 |
| PUT | `/api/problem-solvings/{id}` | 更新 |
| DELETE | `/api/problem-solvings/{id}` | 削除 |
| POST | `/api/problem-solvings/{id}/solutions` | 解決策追加 |
| PUT | `/api/problem-solvings/{id}/solutions/{solutionId}` | 解決策更新 |
| DELETE | `/api/problem-solvings/{id}/solutions/{solutionId}` | 解決策削除 |

---

## やらないこと

- **下書き機能**: statusカラムは設けない
- **チェックリストのDB保存**: 画面表示のみ
- **問題の深刻度評価**: ネガティブな評価を避ける
- **他者との比較機能**: 個人のペースを尊重
- **強制的なリマインダー**: ユーザーの自主性を重視
- **解決策の自動ランキング**: ユーザー自身が判断する
- **専門的な診断やアドバイス**: あくまでセルフケアツール

---

## ファイル構成（新規作成予定）

### ドメイン層

```
app/Domain/Entity/
  └── ProblemSolving.php
  └── ProblemSolvingSolution.php

app/Domain/ValueObject/
  └── ProblemSolvingContent.php
  └── PercentageScore.php

app/Domain/Repository/
  └── ProblemSolvingRepositoryInterface.php
```

### アプリケーション層

```
app/Application/UseCase/ProblemSolving/
  └── CreateProblemSolvingUseCase.php
  └── UpdateProblemSolvingUseCase.php
  └── DeleteProblemSolvingUseCase.php
  └── GetProblemSolvingUseCase.php
  └── GetProblemSolvingListUseCase.php
  └── AddSolutionUseCase.php
  └── UpdateSolutionUseCase.php
  └── DeleteSolutionUseCase.php

app/Application/DTO/
  └── ProblemSolvingData.php
  └── ProblemSolvingSolutionData.php
```

### インフラストラクチャ層

```
app/Infrastructure/Repository/
  └── EloquentProblemSolvingRepository.php

app/Models/（後で app/Infrastructure/Database/Models/ に移行）
  └── ProblemSolving.php
  └── ProblemSolvingSolution.php

database/migrations/（後で app/Infrastructure/Database/Migrations/ に移行）
  └── create_problem_solvings_table.php
  └── create_problem_solving_solutions_table.php
```

### プレゼンテーション層

```
app/Http/Controllers/
  └── ProblemSolvingController.php

app/Http/Requests/ProblemSolving/
  └── CreateProblemSolvingRequest.php
  └── UpdateProblemSolvingRequest.php
  └── AddSolutionRequest.php
  └── UpdateSolutionRequest.php

app/Http/Resources/
  └── ProblemSolvingResource.php
  └── ProblemSolvingSolutionResource.php
```

---

## 実装フェーズ

### Phase 1: 基盤構築

1. マイグレーション作成（problem_solvings, problem_solving_solutions）
2. Eloquentモデル作成
3. ドメインエンティティ作成
4. リポジトリインターフェース・実装作成

### Phase 2: 基本CRUD

1. 問題解決の作成・更新ユースケース実装
2. 問題解決の一覧・詳細取得ユースケース実装
3. 問題解決の削除ユースケース実装
4. 対応するAPIエンドポイント実装

### Phase 3: 解決策機能

1. 解決策追加・更新・削除ユースケース実装
2. 対応するAPIエンドポイント実装

### Phase 4: UI/UX

1. Blade画面の作成（一覧・作成編集・詳細）
2. 未入力タグの表示実装（コラム法参考）
3. チェックリスト表示（画面のみ）

---

## 参考資料

- 洗足ストレスコーピング・サポートオフィス「問題解決ワークシート」

---

## 関連ドキュメント

- [DEVELOPMENT_PLAN.md](./DEVELOPMENT_PLAN.md) - 開発計画
- [DOMAIN_MODEL.md](./DOMAIN_MODEL.md) - ドメインモデル仕様書
- [DB_SCHEMA.md](./DB_SCHEMA.md) - データベーススキーマ定義
- [DIRECTORY_STRUCTURE.md](./DIRECTORY_STRUCTURE.md) - ディレクトリ構成

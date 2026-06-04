
# 概要
このガイドでは、HerokuのPipelineとReview Apps機能を活用し、Laravelアプリケーションにおいて**Pull Requestが作成されるたびに、自動で独立した検証環境を構築する**ワークフローを解説します。

本番・レビュー環境ともに、Heroku の **PHP ビルドパック + `Procfile`** を前提とし、データベースには PostgreSQL を使用します。
本番環境へのデプロイ自動化から、PRごとの検証環境の自動生成・破棄までの一連の流れを網羅します。

# 全体の流れ

**Part 1: 本番環境の準備とデプロイ**
1. Herokuアカウント作成とEcoプラン有効化
2. Heroku CLIのインストールとログイン
3. Heroku Pipelineの作成
4. PipelineへのGitHubリポジトリ連携
5. 本番用アプリケーションの作成と設定
6. **【Laravel + PostgreSQL対応】本番環境の追加設定**
7. 自動デプロイの設定

**Part 2: Review Apps（自動検証環境）の設定**
1. 【Laravel + PostgreSQL対応】app.json ファイルの作成
2. Heroku PipelineでReview Appsを有効化
3. 動作確認（PRの作成）

---

## Part 1: 本番環境の準備とデプロイ

まずは、主となる本番（またはステージング）環境を構築します。

### ステップ1: Herokuアカウント作成とEcoプラン有効化

1. **アカウント作成**: [Heroku公式サイト](https://signup.heroku.com/)でアカウントを作成します。
2. **クレジットカード登録**: [Heroku Account Settings](https://dashboard.heroku.com/account/billing) の "Billing" タブでクレジットカード情報を登録し、アカウントを認証します。これによりEcoプランを含む有料プランが利用可能になります。

**【公式ソース】**
- [Heroku Pricing (料金プラン)](https://www.heroku.com/pricing/)


### ステップ2: Heroku CLIのインストールとログイン

1. **インストール**: [公式マニュアル](https://devcenter.heroku.com/ja/articles/heroku-cli)に従い、Heroku CLIをインストールします。
2. **ログイン**: ターミナルで以下のコマンドを実行し、ブラウザでログインします。

```
heroku login
```

### ステップ3: Heroku Pipelineの作成

Review Appsは「Pipeline」という機能の一部です。Pipelineは、レビュー → ステージング → 本番といった一連のデプロイフローを管理する仕組みです。

1. **Pipelineの作成**: ターミナルで、ローカルのプロジェクトディレクトリに移動し、以下のコマンドを実行します。`<your-pipeline-name>`は好きなパイプライン名に置き換えてください（例: my-laravel-app）。

```
heroku pipelines:create <your-pipeline-name> -s production
```

- `-s production` は、このパイプラインに production (本番) ステージを作成することを意味します。

注意点
-   マニュアルには`termheroku pipelines:create -a example-app`とあるが、`-a`は、既存のアプリケーションを新しく作成するパイプラインに追加するオプションであるため、ここでは上記コマンドのとおり指定せずに空のパイプラインをとりあえず作っている（アプリケーションの作成は後述で行う）

【公式ソース】
- [パイプライン](https://devcenter.heroku.com/ja/articles/pipelines/)

### ステップ4: PipelineへのGitHubリポジトリ連携

作成したPipelineと、あなたのGitHubリポジトリを連携させます。

1. ブラウザで[Herokuダッシュボード](https://dashboard.heroku.com/apps)を開き、先ほど作成したPipelineを選択します。
2. 「Settings」タブを開き、「Connect to GitHub」セクションで、あなたのGitHubリポジトリを検索して接続します。

### ステップ5: 本番用アプリケーションの作成と設定

Pipeline内に、本番環境として稼働するアプリケーションを作成します。

1. **アプリケーションの作成**
```
cd <your-app-dir>
heroku create <your-app-name>
```
- デフォルトの PHP ビルドパックが適用される前提です。

2. パイプラインへの追加とステージ設定
```
heroku pipelines:add <your-pipeline-name> --app <your-app-name> --stage <your-stage-name>
```

【公式ソース】
[heroku apps:create APP](https://devcenter.heroku.com/ja/articles/heroku-cli-commands#heroku-apps-create-app)
[heroku pipelines:add PIPELINE](https://devcenter.heroku.com/ja/articles/heroku-cli-commands#heroku-pipelines-add-pipeline)

3. Procfile を作成し、Herokuにデプロイ
```
echo 'web: vendor/bin/heroku-php-nginx public/' > Procfile

git add .
git commit -m "Procfile"
git push heroku main
```

4. webのプロセスタイプがデプロイされているか、実行中のdyno一覧で確認
```
heroku ps
```

5. **dynoをEcoプランに設定**: 作成したアプリのdyno（サーバー）をEcoプランで起動します。
```
heroku dyno:scale web=1:eco
```


【公式ソース】

### ステップ6: 【Laravel + PostgreSQL対応】本番環境の追加設定

ここからは、Laravel と PostgreSQL を Heroku で動作させるための固有の設定です。

1. **Heroku Postgres アドオンを追加**して、本番用データベースを作成します。

```
heroku addons:create heroku-postgresql:essential-0 --app <your-app-name>
```
- `heroku-postgresql:essential-0` ... 一番安いプラン（20251116現在、有料）

【公式ソース】
[Heroku Postgres](https://elements.heroku.com/addons/heroku-postgresql)

2. **Laravel の APP_KEY を Heroku の環境変数に設定**します。

```
docker compose exec app php artisan key:generate --show

heroku config:set APP_KEY=<上で表示されたキー> --app <new-app-name>
```

※ `base~=`まで全てコピペする

3. **Heroku の `DATABASE_URL` を Laravel が認識できるよう `config/database.php` を修正**します。

`connections` 配列内の `pgsql` 設定で `'url' => env('DATABASE_URL')` を優先させるのが最も簡単です。
```
// config/database.php

'connections' => [
    // ...
    'pgsql' => [
        'driver' => 'pgsql',
        'url' => env('DATABASE_URL'), // この行を追加
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '5432'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
    ],
    // ...
],
```

【公式ソース】

- Heroku Config Vars（`DATABASE_URL` などの環境変数について）
  - `https://devcenter.heroku.com/ja/articles/config-vars`
- Heroku Postgres アドオン（`DATABASE_URL` を自動で付与するデータベースアドオン）
  - `https://devcenter.heroku.com/articles/heroku-postgresql`
- Laravel データベース設定（`config/database.php` で `url` と `DATABASE_URL` を使う例）
  - `https://readouble.com/laravel/10.x/ja/database.html`

4. **Heroku 側の DB 接続ドライバを PostgreSQL に切り替える**

デフォルトのままだと `DB_CONNECTION=sqlite` となり、Heroku 上で `/app/database/database.sqlite` を探しに行ってしまうため、
Heroku の Config Vars で `DB_CONNECTION=pgsql` を明示的に指定します。

```bash
heroku config:set DB_CONNECTION=pgsql --app <your-app-name>
```

セッションをデータベースではなくファイルで管理する場合は、あわせて以下も設定しておくと
`sessions` テーブルが無いことによるエラーを防げます。

```bash
heroku config:set SESSION_DRIVER=file --app <your-app-name>
```

5. **本番 PostgreSQL にマイグレーションとSeederを適用する**

Heroku 上のデータベースには、ローカルとは別にマイグレーションとSeederを流す必要があります。
`heroku run` のオプションと `php artisan` のオプションを区切るために、`--` を挟む点に注意してください。

```bash
cd ~/services/cbt_app

# マイグレーションの実行
heroku run --app <your-app-name> -- php artisan migrate --force

# Seederの実行（全て）
heroku run --app <your-app-name> -- php artisan db:seed --force

# 特定のSeederのみ実行する場合
heroku run --app <your-app-name> -- php artisan db:seed --class=DifficultySeeder --force
heroku run --app <your-app-name> -- php artisan db:seed --class=TagSeeder --force
```

`--force` は本番環境でのマイグレーション・Seeder実行を許可するために必須です。

【公式ソース】

- Heroku CLI `run` コマンド
  - `https://devcenter.heroku.com/articles/heroku-cli-commands#heroku-run`
- Laravel マイグレーション（`php artisan migrate` と `--force` オプション）
  - `https://readouble.com/laravel/10.x/ja/migrations.html`
- Laravel Seeder（`php artisan db:seed` と `--class` オプション）
  - `https://readouble.com/laravel/10.x/ja/seeding.html`


### ステップ7: 自動デプロイの設定

GitHubの特定のブランチ（例: main）にpushされたら、自動で本番アプリにデプロイされるように設定します。

1. [Herokuダッシュボード](https://dashboard.heroku.com/apps)で作成したPipelineを開きます。
2. productionステージにあるアプリをクリックして「Deploy」タブに移動します。
3. "Automatic Deploys" セクションで、デプロイのトリガーとしたいブランチ（例: main）を選び、「Enable Automatic Deploys」をクリックします。

---

## Part 2: Review Apps（自動検証環境）の設定

ここからが、PRに連動する検証環境の設定です。

### ステップ8: 【Laravel + PostgreSQL対応】app.json ファイルの作成

Review Apps がどのような構成で作られるかを定義する設計図となる `app.json` ファイルを、リポジトリのルートディレクトリに作成します。

**app.json の作成例（Laravel + PostgreSQL + Review Apps 用 / PHP ビルドパック前提）**

```json
{
  "name": "cbt-app",
  "description": "Review app for CBT (Cognitive Behavioral Therapy) self-care application.",
  "addons": [
    {
      "plan": "heroku-postgresql:essential-0"
    }
  ],
  "env": {
    "APP_ENV": {
      "description": "検証環境だけど本番と限りなく同じ動作にしたいためproductionとしている",
      "value": "production"
    },
    "APP_DEBUG": "true",
    "APP_KEY": {
      "description": "Review Apps用のため固定キーを設定（本番とは異なる）。本来はsecret generatorを使うべきだが、LaravelのCipher要件を満たすため明示的に指定。",
      "value": "base64:YOUR_GENERATED_KEY_HERE"
    },
    "DB_CONNECTION": "pgsql",
    "SESSION_DRIVER": "file"
  },
  "scripts": {
    "postdeploy": "php artisan migrate --force"
  }
}
```

- **"addons"**: Review App 作成時に、PostgreSQL データベースを自動で追加します。
    - **注意**: `heroku-postgresql:essential-0` は有料プラン（約$5/月、秒単位課金）です。Review Appが起動している間だけ課金されます。こまめに破棄するか、自動破棄設定（後述）を必ず有効にしてください。

- **"env"**:
    - `APP_ENV`: Review Apps では `staging` を指定し、本番（`production`）と Laravel 上で環境を区別します。ログイン画面のテストアカウント表示など、本番以外向けの UI 分岐に利用します。
    - `APP_KEY`: Herokuの自動生成 (`generator: secret`) だとLaravelの暗号化方式と不整合が起きる場合があるため、ローカルで生成したキー (`php artisan key:generate --show`) を固定値として設定します。検証環境用なので固定でもセキュリティリスクは低いです。
    - `APP_DEBUG`: トラブルシューティングのため `true` に設定します（本番同様に隠したい場合は `false`）。
    - `DB_CONNECTION`: `pgsql` を指定し、Heroku Postgresを使用することを明示します。
    - `SESSION_DRIVER`: `file` を指定し、セッション管理をファイルベースにします（DB依存を減らすため）。

- **"scripts" > "postdeploy"**: アプリのデプロイ完了後に、データベースのマイグレーションを自動実行します。

作成したら、この `app.json` ファイルを GitHub リポジトリにコミットし、push してください。

**【公式ソース】**

- **app.json Schema**: [https://devcenter.heroku.com/articles/app-json-schema](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdevcenter.heroku.com%2Farticles%2Fapp-json-schema)


### ステップ9: Heroku PipelineでReview Appsを有効化

1. ブラウザで[Herokuダッシュボード](https://dashboard.heroku.com/apps)から、対象のPipelineを開きます。

2. 「Settings」タブをクリックします。

3. 「Review Apps」セクションで、「Enable Review Apps...」ボタンをクリックします。

4. 表示された画面で、以下の設定を確認・有効化します。

    - app.jsonファイルから設定を継承するようになっていることを確認します。

    - **"Create new review apps for new pull requests automatically"** にチェックを入れます。

    - **"Destroy stale review apps"** を有効にします。これにより、PRがマージ/クローズされたReview Appが自動で削除され、無駄なコストを防げます（重要）。

5. 「Enable」をクリックして設定を完了します。


**【公式ソース】**

- **Review Apps**: [https://devcenter.heroku.com/articles/review-apps](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdevcenter.heroku.com%2Farticles%2Freview-apps)


### ステップ10: 動作確認（PRの作成）

すべての設定が完了したら、実際に動作するか確認しましょう。

1. GitHubリポジトリで、新しいブランチを作成し、何かしらの変更を加えてpushします。

2. そのブランチからmainブランチ（または自動デプロイの対象ブランチ）に対してPull Requestを作成します。

3. HerokuダッシュボードのPipeline画面を確認します。
    - 自動作成を有効にしている場合: 自動的にビルドが始まります。
    - 手動作成の場合: Review Appsの列にPRが表示されるので、「Create review app」ボタンをクリックします。

4. ビルドとデプロイが完了すると、「Open app」ボタンが表示されます。このボタンから、そのPR専用に作られた検証環境にアクセスできます。
    - もしエラー画面（500エラーなど）が出る場合は、Review Appを一度削除し、再作成してみてください（`app.json` の変更反映のため）。


以上で、PRを作成するたびにHeroku上に検証環境が自動で構築され、マージされると自動で破棄されるワークフローが完成しました。

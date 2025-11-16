
# 概要
このガイドでは、HerokuのPipeline、Review Apps、Docker（Container Stack）機能を活用し、Laravelアプリケーションにおいて**Pull Requestが作成されるたびに、自動で独立した検証環境を構築する**ワークフローを解説します。

データベースにはPostgreSQLを使用し、本番環境へのデプロイ自動化から、PRごとの検証環境の自動生成・破棄までの一連の流れを網羅します。

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

# Part 1: 本番環境の準備とデプロイ

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

1. **アプリケーションの作成
```
cd <your-app-dir>
heroku create <your-app-name> --stack=container
```
- --stack=container でDockerコンテナとして動作することを指定します。

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

#### ステップ6: 【Laravel + PostgreSQL対応】本番環境の追加設定

ここからは、LaravelとPostgreSQLをHerokuで動作させるための固有の設定です。

1. Heroku Postgresアドオンを追加して、本番用データベースを作成します。

```
heroku addons:create heroku-postgresql:essential-0 --app <your-app-name>
```
- `heroku-postgresql:essential-0` ... 一番安いプラン（20251116現在）

【公式ソース】
[Heroku Postgres](https://elements.heroku.com/addons/heroku-postgresql)

2. stack: container を使用する場合、ビルド、リリース（マイグレーション）、実行の各プロセスを heroku.yml ファイルで定義します。プロジェクトのルートディレクトリにこのファイルを作成してください。
```
# heroku.yml
build:
  docker:
    web: Dockerfile
release:
  image: web
  command:
    - php artisan migrate --force
run:
  web: vendor/bin/heroku-php-apache2 public/
```
- **build**: Dockerfile を元に web プロセス用のイメージをビルドします。
- **release**: デプロイのリリースフェーズで、データベースのマイグレーションを自動実行します。--force は本番環境での実行に必須です。
- **run**: Apacheサーバーを起動し、Laravelの公開ディレクトリ public/ をドキュメントルートに設定します。

Laravelの動作に必須のアプリケーションキーをHerokuの環境変数に設定します。
```
# <your-app-name>を実際のアプリ名に置き換えてください
heroku config:set APP_KEY=$(php artisan key:generate --show) --app <your-app-name>
```

Herokuの DATABASE_URL をLaravelが認識できるよう config/database.php を修正します。

connections 配列内の pgsql 設定で 'url' => env('DATABASE_URL') を優先させるのが最も簡単です。
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

#### ステップ7: 自動デプロイの設定

GitHubの特定のブランチ（例: main）にpushされたら、自動で本番アプリにデプロイされるように設定します。

1. [Herokuダッシュボード](https://dashboard.heroku.com/apps)で作成したPipelineを開きます。
2. productionステージにあるアプリをクリックして「Deploy」タブに移動します。
3. "Automatic Deploys" セクションで、デプロイのトリガーとしたいブランチ（例: main）を選び、「Enable Automatic Deploys」をクリックします。

---

### Part 2: Review Apps（自動検証環境）の設定

ここからが、PRに連動する検証環境の設定です。

#### ステップ8: 【Laravel + PostgreSQL対応】app.json ファイルの作成

Review Appsがどのような構成で作られるかを定義する設計図となるapp.jsonファイルを、リポジトリのルートディレクトリに作成します。

**app.jsonの作成例（Laravel + Docker + PostgreSQL用）**

code JSON

downloadcontent_copy

expand_less

    `{   "name": "Your App Name for Review",   "description": "A review app for my awesome project.",   "stack": "container",   "formation": {     "web": {       "quantity": 1,       "size": "eco"     }   },   "addons": [     {       "plan": "heroku-postgresql:hobby-dev"     }   ],   "env": {     "APP_KEY": {       "description": "Laravel Application Key",       "generator": "secret"     }   },   "scripts": {     "postdeploy": "php artisan migrate --force"   } }`


- **"stack": "container"**: Review AppをDockerコンテナでビルド・実行します。

- **"formation"**: Review AppをEcoプランのdynoで起動し、コストを抑えます。

- **"addons"**: Review App作成時に、無料プランのPostgreSQLデータベースを自動で追加します。

- **"env"**: Review Appごとにユニークな APP_KEY を自動で生成・設定します。

- **"scripts" > "postdeploy"**: アプリのデプロイ完了後に、データベースのマイグレーションを自動実行します。


作成したら、このapp.jsonファイルをGitHubリポジトリにコミットし、pushしてください。

**【公式ソース】**

- **app.json Schema**: [https://devcenter.heroku.com/articles/app-json-schema](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdevcenter.heroku.com%2Farticles%2Fapp-json-schema)


#### ステップ9: Heroku PipelineでReview Appsを有効化

1. ブラウザで[Herokuダッシュボード](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdashboard.heroku.com%2Fpipelines)から、対象のPipelineを開きます。

2. 「Settings」タブをクリックします。

3. 「Review Apps」セクションで、「Enable Review Apps...」ボタンをクリックします。

4. 表示された画面で、以下の設定を確認・有効化します。

    - app.jsonファイルから設定を継承するようになっていることを確認します。

    - **"Create new review apps for new pull requests automatically"** にチェックを入れます。

    - **"Destroy stale review apps"** を有効にします。これにより、PRがマージ/クローズされたReview Appが自動で削除され、無駄なコストを防げます。

5. 「Enable」をクリックして設定を完了します。


**【公式ソース】**

- **Review Apps**: [https://devcenter.heroku.com/articles/review-apps](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdevcenter.heroku.com%2Farticles%2Freview-apps)


#### ステップ10: 動作確認（PRの作成）

すべての設定が完了したら、実際に動作するか確認しましょう。

1. GitHubリポジトリで、新しいブランチを作成し、何かしらの変更を加えてpushします。

2. そのブランチからmainブランチ（または自動デプロイの対象ブランチ）に対してPull Requestを作成します。

3. GitHubのPR画面に戻ると、Herokuによるチェックが始まり、「Deploying to review app...」といったステータスが表示されます。

4. ビルドとデプロイが完了すると、ステータスが成功に変わり、「View deployment」ボタンが表示されます。このボタンから、そのPR専用に作られた検証環境にアクセスできます。


以上で、PRを作成するたびにHeroku上にEcoプランベースの検証環境が自動で構築され、マージされると自動で破棄されるワークフローが完成しました。

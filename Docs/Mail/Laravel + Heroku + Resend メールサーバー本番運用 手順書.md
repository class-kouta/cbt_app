
# 🚀 Laravel + Heroku + Resend メールサーバー本番運用 手順書

## 📝 前提条件
*   LaravelプロジェクトがHeroku上でデプロイされ、動いている状態であること。
*   GitとHeroku CLIがローカル環境にインストールされていること。

---

## ステップ1：Xserverで独自ドメインを取得する
まずはインターネット上の住所となるドメインを購入します。

1.  **Xserverドメインにアクセス**[Xserverドメイン公式サイト](https://www.xdomain.ne.jp/)を開きます。
2.  **ドメインの検索と購入**
    *   検索窓に希望の文字列（アプリ名など）を入力し、空いているドメインを探します（コスパと信頼性の面で `.com` や `.net` がおすすめです）。
    *   「取得手続きに進む」をクリックし、Xserverアカウントを作成（またはログイン）します。
    *   お支払い情報を入力して購入を完了させます。
    *   ※Xserverドメインは**「WHOIS情報公開代行」が標準で無料（自動適用）**なので、個人情報漏洩の心配はありません。

---

## ステップ2：Herokuのドメインを独自ドメインに変更する（Basicプラン契約含む）

### なぜこの手順が必要なのか？

技術的には、**「メールを送る機能だけ」を考えるなら、Heroku側のドメインを変更する必要はありません**。Resend（メールの送信元）とHeroku（Webサイトの置き場所）は完全に独立しているため、Webサイトのドメインが `xxx.herokuapp.com` のままでも、メール自体は `info@your-app.com` から正常に送信できます。

しかし、本番運用では**メールの送信元ドメインとWebサイトのURLを統一する**ことが必須レベルで重要です。理由は**ユーザーからの信用（セキュリティ面）**にあります。

例えば、ユーザーに「会員登録完了メール」を送るとします。

*   **送信元**: `info@your-app.com` （カッコいい独自ドメイン）
*   **メール本文のリンク**: 「以下のURLからログインしてください 👉 `https://your-app.herokuapp.com/login` 」

ドメインがバラバラだと、フィッシング詐欺が多い昨今、ユーザーから**「公式っぽいアドレスから来たメールなのに、リンク先のURLが全然違う！詐欺かも！？」**と警戒されてしまいます。せっかく年間1,500円払って独自ドメインを買ったのですから、WebサイトのURLも `https://your-app.com` に統一するのが本番運用におけるベストプラクティス（鉄則）です。

そのため、Resendの設定に進む前に、まずHeroku側のドメインを独自ドメインに切り替えてしまいましょう。

### 手順

#### 1. HerokuをEcoプラン（月額$5）以上にする
Herokuで独自ドメインを「HTTPS（暗号化された安全な通信）」で動かすには、**HerokuのEcoプラン以上**が必要です（2024年8月6日以降、Ecoプランでも自動SSL証明書管理 ACM が利用可能になりました）。

*   Herokuのダッシュボードで対象アプリを開き、**[Resources]** タブの `Change Dyno Type` などからプラン変更画面に進みます。
*   `Eco`プラン（月額 $5）以上を選択します。
*   ※常時稼働させたい場合は `Basic`プラン（月額 $7）以上推奨。Ecoはスリープあり。

#### 2. Herokuにドメインを登録する
*   Herokuのダッシュボードでアプリの **[Settings]** タブを開きます。
*   下にスクロールして **[Domains]** セクションの `Add domain` をクリック。
*   ステップ1で取得したドメイン（例: `your-app.com` または `www.your-app.com`）を入力してNext。
*   画面に **「DNS Target」** という文字列（例: `whispering-willow-xxxx.herokudns.com`）が表示されるので、これをコピーします。

#### 3. 【超重要】ネームサーバーがどこを向いているか確認する

DNSレコードを設定する前に、**そのドメインを管理しているネームサーバー（NS）がどこを向いているか**を必ず確認してください。ここを間違えると、いくらDNSレコードを設定しても永遠に世界に反映されません。

##### 3-1. 現在のネームサーバーを確認する
ターミナルで以下のコマンドを実行します。

```bash
dig NS あなたのドメイン.com +short
```

返ってくる結果によって、設定すべき場所が変わります。

| 返ってきた値 | 意味 | DNSレコード設定すべき場所 |
| :--- | :--- | :--- |
| `ns1〜ns5.xdomain.ne.jp` | **Xserverドメイン側** が管理 | Xserverドメインの「ドメイン管理パネル」 |
| `ns1〜ns5.xserver.jp` | **Xserverレンタルサーバー側** が管理 | Xserverアカウントの「サーバーパネル」 |

##### 3-2. レンタルサーバーを契約していないのに `ns1〜ns5.xserver.jp` になっていた場合
Xserverドメイン取得時のキャンペーン等で、自動的にレンタルサーバー側のNSが設定されているケースがあります。レンタルサーバーを使わない場合は、以下の手順でネームサーバーをXserverドメイン側（`ns1〜ns5.xdomain.ne.jp`）に戻します。

1.  [Xserverアカウント](https://secure.xserver.ne.jp/xapanel/login/xdomain/) にログイン
2.  上部メニューから「**ドメイン**」 → 「**契約管理**」（または「ご契約一覧」）を開く
3.  対象のドメイン名をクリックして「**ドメイン詳細**」を開く
4.  「ネームサーバー情報」セクションにある「**ネームサーバーの確認・変更**」をクリック
5.  「**その他のサービスで利用する**」を選択し、以下を入力して保存
    ```
    ネームサーバー1: ns1.xdomain.ne.jp
    ネームサーバー2: ns2.xdomain.ne.jp
    ネームサーバー3: ns3.xdomain.ne.jp
    ネームサーバー4: ns4.xdomain.ne.jp
    ネームサーバー5: ns5.xdomain.ne.jp
    ```
6.  ※ネームサーバー変更の反映には**最大72時間**かかる場合があります（実際は数分〜数時間で反映されることがほとんど）。

#### 4. XserverのDNSにCNAMEレコードを設定する
ネームサーバーが正しく向いていることを確認したら、独自ドメインへのアクセスをHerokuへ流すための設定を行います。

*   Xserverドメインの「[ドメイン管理パネル](https://secure.xserver.ne.jp/xapanel/login/xdomain/)」にログインし、対象ドメインの「DNSレコード設定」を開きます。
*   以下のレコードを追加します。
    *   ホスト名: （空欄、または `www` などHerokuで登録したもの）
    *   タイプ: **`CNAME`**
    *   値: **コピーしたHerokuのDNS Target**

#### 5. DNSの反映状況を確認する
設定後、以下のコマンドで世界中のDNSサーバーから見えているか確認できます。

```bash
dig あなたのドメイン.com @8.8.8.8 +short  # Google DNSから問い合わせ
dig あなたのドメイン.com @1.1.1.1 +short  # Cloudflare DNSから問い合わせ
dig +trace あなたのドメイン.com           # ルートから辿って委任の流れを確認
```

GUIで確認したい場合は以下が便利です。
*   [whatsmydns.net](https://www.whatsmydns.net/) - 世界中のDNSサーバーから一気に伝播チェック
*   [dnschecker.org](https://dnschecker.org/) - 同じく伝播確認系

#### 6. SSL（HTTPS）を有効化する
**重要：HerokuのACM（Automated Certificate Management）は、デフォルトで無効になっている場合があります**。手動で有効化が必要です。

##### 6-1. ACMの状態を確認
```bash
heroku certs:auto --app あなたのアプリ名
```
出力に `Automatic Certificate Management is disabled on ...` と表示されたらACMが無効化されています。

##### 6-2. ACMを有効化
```bash
heroku certs:auto:enable --app あなたのアプリ名
```
これでLet's Encryptが自動で証明書を発行してくれます（**5〜30分程度**かかります）。

##### 6-3. 証明書発行状況を確認
```bash
heroku certs:auto --app あなたのアプリ名
```
`Status: Cert issued` になれば発行完了！
`https://あなたのドメイン.com` でブラウザからアクセスできるようになります。

> ⚠️ **ACMでよくあるエラー**
> - `DNS Verification Failed`: DNSがまだ伝播していない or CNAMEが正しくない
> - `Failing`: 数分待ってから `heroku certs:auto:refresh --app アプリ名` で再試行

> **メモ**: Laravel側の `APP_URL` 環境変数の更新は、メール用の環境変数とまとめて**ステップ4**で行います。

---

## ステップ3：Resendの契約とドメイン認証（DNS設定）
取得したドメインでメールを送るため、Resend側の設定と「私がこのドメインの持ち主です」という証明（DNS設定）を行います。

1.  **Resendアカウントの作成**
    *   [Resend公式サイト](https://resend.com/)でアカウントを作成します（GitHub連携が便利です）。
2.  **ドメインの追加**
    *   左メニューの **[Domains]** をクリックし、`Add Domain` ボタンを押します。
    *   ステップ1で取得したドメイン（例：`your-app.com`）を入力し、リージョンはそのまま（`us-east-1`等）で追加します。
    *   **DNSレコード（TXTレコードやMXレコードなど）のリスト**が表示されるので、この画面を開いたままにします。
3.  **Xserver側でDNSレコードを追加（超重要）**
    *   別のタブでXserverドメインの「[ドメイン管理パネル](https://secure.xserver.ne.jp/xapanel/login/xdomain/)」にログインします（ステップ2でCNAMEを追加したのと同じ画面です）。
    *   取得したドメインの「DNSレコード設定」を開き、Resendの画面に表示されているレコードを**1つずつ全て**追加していきます。
        *   *例：タイプが `TXT`、ホスト名が `resend._domainkey`、値が `p=...` なら、そのままコピペして追加。*
4.  **Resend側で認証の確認**
    *   レコードを追加し終えたら、Resendの画面に戻り `Verify DNS records` をクリックします。
    *   ステータスが **「Verified（緑色）」** になればドメインの認証は完了です！（※DNSの反映には数分〜最大数時間かかる場合があります）。
5.  **APIキーの発行**
    *   Resendの左メニュー **[API Keys]** から `Create API Key` をクリック。
    *   わかりやすい名前（例：`heroku-prod`）を付け、権限は「Sending access」で作成します。
    *   発行された **`re_...` から始まるAPIキーをコピーしてメモ帳などに保存**しておきます（二度と表示されません）。

---

## ステップ4：アプリケーション側の修正
LaravelがResendを使ってメールを送れるよう、コードと環境変数の両方を修正します。

### 4-1. Resend公式PHPパッケージのインストール（ローカル作業）
ターミナルを開き、Laravelプロジェクトのディレクトリで以下を実行します。

```bash
composer require resend/resend-php
```

### 4-2. 設定ファイルの確認（Laravel 10をご利用の場合のみ）
※Laravel 11をお使いの場合はこの手順はスキップしてOKです。
`config/mail.php` を開き、`mailers` 配列に以下を追加します。

```php
'mailers' => [
    // ...他の設定
    'resend' => [
        'transport' => 'resend',
    ],
],
```

### 4-3. Herokuへデプロイ
パッケージの追加（`composer.json`等の変更）をGitにコミットし、HerokuへPushします。

```bash
git add .
git commit -m "Add Resend package for mail"
git push heroku main
```

### 4-4. Herokuの環境変数（Config Vars）を設定する
Heroku上のLaravelがResendを使い、かつ独自ドメインで動作するように、環境変数を設定します。

*   Herokuダッシュボードで対象アプリの **[Settings]** タブを開き、**[Reveal Config Vars]** をクリック。
*   以下のキーと値を入力して `Add` します。

| KEY | VALUE |
| :--- | :--- |
| `MAIL_MAILER` | **`resend`** |
| `RESEND_KEY` | **`re_...`** （ステップ3でメモしたAPIキー）<br>※ `config/services.php` で `env('RESEND_KEY')` を参照しているため、キー名は `RESEND_KEY` であることに注意。 |
| `MAIL_FROM_ADDRESS` | **`info@あなたのドメイン.com`** （※ステップ1で取得したドメイン） |
| `MAIL_FROM_NAME` | **`あなたのアプリ名`** |
| `APP_URL` | **`https://あなたのドメイン.com`** （※ステップ2で連携したドメイン） |

*(※もし `MAIL_HOST` や `MAIL_PASSWORD` などの古い設定が残っていたら、混乱を防ぐため「×」ボタンで削除してしまって大丈夫です)*

> **`APP_URL` を忘れずに更新**：これでメール内のリンク生成（`route()`関数など）が自動で新しい独自ドメインに切り替わり、メール本文のURLとサイトのURLが一致するようになります。

---

## ステップ5：本番環境でのテスト送信
最後に、Heroku上で実際にメールが飛ぶか確認します。

1.  **HerokuのTinkerを起動**
    ターミナルからHeroku上のLaravelにアクセスします。
    ```bash
    heroku run --app cbt-app-kouta php artisan tinker
    ```
2.  **テストメールを送信**
    Tinkerが起動したら、以下のコードを1行ずつ（または一気に）コピペしてEnterを押します。
    ```php
    Mail::raw('Resendからのテストメールです！', function ($message) {
        $message->to('あなたが普段使っているメアド@gmail.com')
                ->subject('本番環境からのテスト');
    });
    ```
3.  **受信確認**
    指定したGmail等にメールが届けば**設定完了**です！
    *迷惑メールフォルダに入らず、送信元が「info@あなたのドメイン.com」になっていれば大成功です。*

---

### 💡 運用時のワンポイントアドバイス
*   **メールのログ確認**: ユーザーから「メールが届かない」と問い合わせが来た時は、Resendのダッシュボードの **[Emails]** メニューを見てください。相手のアドレスが間違っている（Bounced）のか、無事届いている（Delivered）のかがリアルタイムでわかります。

手順は以上です！これでプロのサービスと同等のメール配信インフラが整いました。開発頑張ってください！

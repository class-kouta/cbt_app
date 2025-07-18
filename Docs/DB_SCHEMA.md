# Database Schema Definition

最終決定したテーブル構成をまとめた DB 定義書です。スマートフォンでも読みやすいよう、各カラムはリスト形式で記述しています。

---
## users
- id — bigint, 主キー, AUTO_INCREMENT
- name — varchar(255), NOT NULL
- email — varchar(255), UNIQUE, NOT NULL
- email_verified_at — timestamp, NULL 可
- password — varchar(255), NOT NULL
- remember_token — varchar(100), NULL 可
- created_at / updated_at — timestamp (Laravel 標準)

---
## difficulties
- id — bigint, 主キー（例: 1=小, 2=中, 3=大）
- name — varchar(10), NOT NULL（表示名: 小 / 中 / 大）
- points — tinyint, NOT NULL（1 / 2 / 3 など重み付け）
- color — char(7), NULL 可（例: #FFAA00）
- created_at / updated_at — timestamp

---
## tags
- id — bigint, 主キー
- name — varchar(50), UNIQUE, NOT NULL
- created_at / updated_at — timestamp

---
## todos
- id — bigint, 主キー
- user_id — bigint, NOT NULL, 外部キー → users.id
- difficulty_id — bigint, 外部キー → difficulties.id
- content — text, NOT NULL（タスク内容）
- completed_at — timestamp, NULL 可（NULL = 未完了）
- created_at / updated_at — timestamp

インデックス:
- (user_id, completed_at)
- completed_at

---
## todo_tag (中間テーブル)
- todo_id — bigint, 複合主キー, 外部キー → todos.id
- tag_id — bigint, 複合主キー, 外部キー → tags.id

---
### 補足メモ
- `todo_tag` は複合主キー (todo_id, tag_id) で重複登録を防止します。
- Google などの SNS 認証を追加する場合は、必要に応じて `users` に `google_id` を追加するか、`social_accounts` テーブルを拡張してください。
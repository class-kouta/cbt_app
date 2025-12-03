# Database Schema Definition

最終決定したテーブル構成をまとめた DB 定義書です。スマートフォンでも読みやすいよう、各カラムはリスト形式で記述しています。

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
- difficulty_id — bigint, 外部キー → difficulties.id
- content — text, NOT NULL（タスク内容）
- completed_at — timestamp, NULL 可（NULL = 未完了）
- created_at / updated_at — timestamp

インデックス:
- completed_at

---
## todo_tag (中間テーブル)
- todo_id — bigint, 複合主キー, 外部キー → todos.id
- tag_id — bigint, 複合主キー, 外部キー → tags.id

---
## coping_tags（コーピング専用タグ）
- id — bigint, 主キー
- name — varchar(50), UNIQUE, NOT NULL
- created_at / updated_at — timestamp

---
## copings（コーピングリスト）
- id — bigint, 主キー
- content — text, NOT NULL（コーピング内容）
- point — integer, DEFAULT 0（お気に入りポイント、高いほど上に表示）
- created_at / updated_at — timestamp

インデックス:
- point, created_at（ソート用）

---
## coping_coping_tag (中間テーブル)
- coping_id — bigint, 複合主キー, 外部キー → copings.id
- coping_tag_id — bigint, 複合主キー, 外部キー → coping_tags.id

---
## quick_tasks（クイックタスク）
- id — bigint, 主キー
- content — text, NOT NULL（タスク内容のテンプレート）
- difficulty_id — bigint, NULL可, 外部キー → difficulties.id（デフォルト難易度）
- created_at / updated_at — timestamp

**用途:**
- 頻繁に登録するタスク（家事・育児など）を事前登録しておく
- TODOページでワンタップで入力フォームに転記できる
- 難易度とタグを事前設定しておくことで、さらに素早くTODO登録が可能

---
## quick_task_tag（クイックタスク-タグ中間テーブル）
- quick_task_id — bigint, 複合主キー, 外部キー → quick_tasks.id
- tag_id — bigint, 複合主キー, 外部キー → tags.id

---
## writing_disclosures（筆記開示）
- id — bigint, 主キー
- content — text, NOT NULL（メモ内容）
- created_at / updated_at — timestamp

**用途:**
- 反芻思考が止まらない時に、頭の中のモヤモヤを書き出して外在化する
- 思考を言語化することで、客観的に見つめ直すことができる
- 心理療法における「筆記開示（expressive writing）」の技法を実践

---
### 補足メモ
- `todo_tag` は複合主キー (todo_id, tag_id) で重複登録を防止します。
- `coping_coping_tag` は複合主キー (coping_id, coping_tag_id) で重複登録を防止します。
- copingsのタグはtodosのタグとは独立して管理されます。
- `quick_tasks` は TODO のテンプレートとして機能し、TODO とは独立して管理されます。
- `writing_disclosures` は反芻思考の外在化のための筆記開示記録として機能します。
# Database Schema Definition

最終決定したテーブル構成をまとめた DB 定義書です。スマートフォンでも読みやすいよう、各カラムはリスト形式で記述しています。

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
## columns（コラム法/7カラム法）
- id — bigint, 主キー
- situation — text, NOT NULL（状況）
- emotion — text, NULL可（感情）
- automatic_thought — text, NULL可（自動思考）
- evidence_for — text, NULL可（根拠）
- evidence_against — text, NULL可（反証）
- balanced_thought — text, NULL可（適応的思考）
- action_plan — text, NULL可（今後の対応）
- created_at / updated_at — timestamp

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
## problem_solvings（問題解決法）
- id — bigint, 主キー
- problem_situation — text, NOT NULL（問題状況）
- improved_image — text, NULL可（改善イメージ）
- action_plan — text, NULL可（実行計画）
- reflection — text, NULL可（振り返り）
- created_at / updated_at — timestamp

---
## problem_solving_solutions（問題解決法の解決策）
- id — bigint, 主キー
- problem_solving_id — bigint, 外部キー → problem_solvings.id
- content — text, NOT NULL（解決策の内容）
- effectiveness — tinyint, NULL可（効果的か 0-100%）
- feasibility — tinyint, NULL可（実行可能か 0-100%）
- sort_order — integer, NOT NULL（表示順 1-7）
- created_at / updated_at — timestamp

---
## simple_notepads（シンプルメモ帳）
- id — bigint, 主キー
- content — text, NOT NULL（メモ内容）
- created_at / updated_at — timestamp

**用途:**
- 各機能に当てはまらない、ただ思い浮かんだ思考を外在化するため
- 特定の心理療法に紐づかないシンプルなメモ書き
- 思いついたことをとりあえず書き留めておく

---
## support_networks（サポートネットワーク）
- id — bigint, 主キー
- name — varchar(100), NOT NULL（サポート者の名前）
- point — integer, DEFAULT 0（お気に入りポイント、高いほど上に表示）
- created_at / updated_at — timestamp

インデックス:
- point, created_at（ソート用）

**用途:**
- 自分をサポートしてくれる人々（家族、友人、専門家など）を管理する
- 困った時に頼れる人のリストを可視化する
- お気に入りポイントで頼りになる度合いを記録

---
### 補足メモ
- `coping_coping_tag` は複合主キー (coping_id, coping_tag_id) で重複登録を防止します。
- copingsのタグは独立して管理されます。
- `writing_disclosures` は反芻思考の外在化のための筆記開示記録として機能します。
- `problem_solvings` は認知行動療法の問題解決法を実践するための記録です。
- `simple_notepads` は特定の心理療法に紐づかないシンプルなメモ帳機能です。

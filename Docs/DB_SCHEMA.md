# Database Schema Definition

最終決定したテーブル構成をまとめた DB 定義書です。スマートフォンでも読みやすいよう、各カラムはリスト形式で記述しています。

---
## Laravel標準テーブル

### users（ユーザー）
- id — bigint, 主キー
- name — varchar(255), NOT NULL
- email — varchar(255), UNIQUE, NOT NULL
- email_verified_at — timestamp, NULL可
- password — varchar(255), NOT NULL
- remember_token — varchar(100), NULL可
- created_at / updated_at — timestamp

---
### password_reset_tokens（パスワードリセットトークン）
- email — varchar(255), 主キー
- token — varchar(255), NOT NULL
- created_at — timestamp, NULL可

---
### sessions（セッション）
- id — varchar(255), 主キー
- user_id — bigint, NULL可, インデックス
- ip_address — varchar(45), NULL可
- user_agent — text, NULL可
- payload — longtext, NOT NULL
- last_activity — integer, インデックス

---
### cache（キャッシュ）
- key — varchar(255), 主キー
- value — mediumtext, NOT NULL
- expiration — integer, NOT NULL

---
### cache_locks（キャッシュロック）
- key — varchar(255), 主キー
- owner — varchar(255), NOT NULL
- expiration — integer, NOT NULL

---
### jobs（キュー）
- id — bigint, 主キー
- queue — varchar(255), インデックス, NOT NULL
- payload — longtext, NOT NULL
- attempts — unsigned tinyint, NOT NULL
- reserved_at — unsigned integer, NULL可
- available_at — unsigned integer, NOT NULL
- created_at — unsigned integer, NOT NULL

---
### job_batches（ジョブバッチ）
- id — varchar(255), 主キー
- name — varchar(255), NOT NULL
- total_jobs — integer, NOT NULL
- pending_jobs — integer, NOT NULL
- failed_jobs — integer, NOT NULL
- failed_job_ids — longtext, NOT NULL
- options — mediumtext, NULL可
- cancelled_at — integer, NULL可
- created_at — integer, NOT NULL
- finished_at — integer, NULL可

---
### failed_jobs（失敗ジョブ）
- id — bigint, 主キー
- uuid — varchar(255), UNIQUE, NOT NULL
- connection — text, NOT NULL
- queue — text, NOT NULL
- payload — longtext, NOT NULL
- exception — longtext, NOT NULL
- failed_at — timestamp, NOT NULL, DEFAULT CURRENT_TIMESTAMP

---
## アプリケーションテーブル

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
- stressor_and_response_id — bigint, NULL可, 外部キー → stressor_and_responses.id（転記元のストレッサーとストレス反応ID）
- created_at / updated_at — timestamp

**補足:**
- stressor_and_response_id は転記元のストレッサーとストレス反応のIDを保持
- ストレッサーが削除された場合はSET NULLになる
- この値があることで「転記済み」かどうかを判定できる

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
- created_at / updated_at — timestamp

**補足:**
- 実行計画と振り返りは `problem_solving_plans` テーブルに移行済み（複数計画対応）

---
## problem_solving_plans（問題解決法の実行計画・振り返り）
- id — bigint, 主キー
- problem_solving_id — bigint, 外部キー → problem_solvings.id（CASCADE DELETE）
- plan_number — unsigned integer, NOT NULL, DEFAULT 1（計画番号、1から始まる）
- action_plan — text, NULL可（実行計画）
- reflection — text, NULL可（振り返り）
- improvement_level — unsigned tinyint, NULL可（改善レベル 1-10）
- created_at / updated_at — timestamp

ユニーク制約:
- (problem_solving_id, plan_number)

---
## exposures（エクスポージャー療法）
- id — bigint, 主キー
- member_id — bigint, 外部キー → members.id, NOT NULL
- avoidance_target — text, NOT NULL（回避していること）
- notes — text, NULL可（備考）
- created_at / updated_at — timestamp

---
## exposure_hierarchy_items（不安階層表）
- id — bigint, 主キー
- exposure_id — bigint, 外部キー → exposures.id（CASCADE DELETE）
- content — text, NOT NULL
- expected_suds — unsigned tinyint, NULL可（不安レベル 0-100、5刻み）
- sort_order — integer, NOT NULL
- created_at / updated_at — timestamp

---
## exposure_sessions（実施記録）
- id — bigint, 主キー
- exposure_id — bigint, 外部キー → exposures.id（CASCADE DELETE）
- hierarchy_item_id — bigint, NULL可, 外部キー → exposure_hierarchy_items.id（SET NULL）
- session_number — unsigned integer, NOT NULL, DEFAULT 1
- suds_after — unsigned tinyint, NULL可（実施後の不安レベル 0-100、5刻み）
- reflection — text, NULL可（振り返り）
- created_at / updated_at — timestamp

ユニーク制約:
- (exposure_id, session_number)

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
## stressor_and_responses（ストレッサーとストレス反応）
- id — bigint, 主キー
- stressor — text, NOT NULL（ストレッサー：ストレスの原因）
- cognition — text, NULL可（認知：自動思考）
- mood — text, NULL可（気分・感情）
- body_reaction — text, NULL可（身体反応）
- behavior — text, NULL可（行動）
- created_at / updated_at — timestamp

**用途:**
- ストレスの原因（ストレッサー）と、それに対する反応を4つの側面から記録
- 認知行動療法におけるストレス反応の理解と分析
- ストレスへの気づきを高め、適切なコーピング選択の基盤となる

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

---
## early_maladaptive_schemas（早期不適応スキーマ）
- id — bigint, 主キー

### 第1領域：切断と拒絶
- abandonment — tinyint, NULL可（見捨てられ/不安定スキーマ 0-100%）
- mistrust_abuse — tinyint, NULL可（不信/虐待スキーマ 0-100%）
- emotional_deprivation — tinyint, NULL可（情緒的剥奪スキーマ 0-100%）
- defectiveness_shame — tinyint, NULL可（欠陥/恥スキーマ 0-100%）
- social_isolation — tinyint, NULL可（社会的孤立/疎外スキーマ 0-100%）

### 第2領域：自律性と機能の障害
- dependence_incompetence — tinyint, NULL可（依存/無能スキーマ 0-100%）
- vulnerability_to_harm — tinyint, NULL可（損害や疾病に対する脆弱性スキーマ 0-100%）
- enmeshment — tinyint, NULL可（巻き込まれ/未発達な自己スキーマ 0-100%）
- failure — tinyint, NULL可（失敗スキーマ 0-100%）

### 第3領域：制約の欠如
- entitlement_grandiosity — tinyint, NULL可（権利要求/尊大さスキーマ 0-100%）
- insufficient_self_control — tinyint, NULL可（自制と自律の欠如スキーマ 0-100%）

### 第4領域：他者への志向
- subjugation — tinyint, NULL可（服従スキーマ 0-100%）
- self_sacrifice — tinyint, NULL可（自己犠牲スキーマ 0-100%）
- approval_seeking — tinyint, NULL可（承認欲求/評価の追求スキーマ 0-100%）

### 第5領域：過剰警戒と抑制
- negativity_pessimism — tinyint, NULL可（否定/悲観スキーマ 0-100%）
- emotional_inhibition — tinyint, NULL可（感情抑制スキーマ 0-100%）
- unrelenting_standards — tinyint, NULL可（厳密な基準/過度の批判スキーマ 0-100%）
- punitiveness — tinyint, NULL可（罰への懲罰的志向スキーマ 0-100%）

- created_at / updated_at — timestamp

**用途:**
- スキーマ療法における18の早期不適応スキーマの囚われ度を記録
- 各スキーマに対して0-100%（5%刻み）で自己評価
- 30秒ごとに自動保存し、ユーザーの入力を保持

---
## tags（汎用タグ）
- id — bigint, 主キー
- name — varchar(50), UNIQUE, NOT NULL
- created_at / updated_at — timestamp

**初期データ:**
- 人間関係、勉強、キャリア、学校、恋愛、夫婦、家庭、育児、健康、お金、仕事

**用途:**
- ストレッサー、コラム法、問題解決法に共通で使用できるタグ
- 記録をカテゴリー別に分類するためのラベル

---
## stressor_and_response_tag (中間テーブル)
- stressor_and_response_id — bigint, 複合主キー, 外部キー → stressor_and_responses.id
- tag_id — bigint, 複合主キー, 外部キー → tags.id

---
## column_tag (中間テーブル)
- column_id — bigint, 複合主キー, 外部キー → columns.id
- tag_id — bigint, 複合主キー, 外部キー → tags.id

---
## problem_solving_tag (中間テーブル)
- problem_solving_id — bigint, 複合主キー, 外部キー → problem_solvings.id
- tag_id — bigint, 複合主キー, 外部キー → tags.id

---
## dialogue_works（対話ワーク）
- id — bigint, 主キー
- type — varchar(20), NOT NULL, DEFAULT 'schema'（対話ワーク種別: schema or mode）
- mode_category — varchar(50), NULL可（モードカテゴリ: 傷ついた子どもモード/傷つける大人モード/いたたけない対処モード）
- mode_name — varchar(100), NULL可（ユーザーがつけたモードの具体的な名前）
- content — text, NOT NULL（対話ワーク内容、JSON形式で対話エントリ配列を保持）
- created_at / updated_at — timestamp

インデックス:
- type（種別フィルタ用）

**用途:**
- スキーマ療法における対話ワークを記録
- type='schema': ヘルシーサイドとスキーマサイドの対話のワーク
- type='mode': モードワークの対話のワーク（ヘルシーな大人モードとユーザー指定モードの対話）

**補足:**
- `type='mode'`の場合、`mode_category`と`mode_name`は必須です。
- `type='schema'`の場合、これらのフィールドは使用されず`NULL`になります。

---
## stress_person_encyclopedias（ストレス人物図鑑）
- id — bigint, 主キー
- member_id — bigint, 外部キー → members.id, NOT NULL, ON DELETE CASCADE
- name — varchar(255), NOT NULL（名前）
- relationship — text, NULL可（関係性）
- difficult_traits — text, NULL可（苦手な特徴）
- my_reaction — text, NULL可（自分の反応）
- coping_strategy — text, NULL可（対応方針）
- notes — text, NULL可（備考）
- created_at / updated_at — timestamp

インデックス:
- member_id
- member_id, created_at

**用途:**
- ストレスを感じる人物について整理・記録する機能
- 名前のみ必須、その他の項目は任意

---

### 補足メモ
- Laravel標準テーブルとして、ユーザー認証、パスワードリセット、セッション、キャッシュ、キュー関連のテーブルを追記しました。
- `coping_coping_tag` は複合主キー (coping_id, coping_tag_id) で重複登録を防止します。
- copingsのタグは独立して管理されます（`coping_tags`テーブル）。
- `tags` は汎用タグとして、ストレッサー、コラム法、問題解決法で共通利用されます。
- `writing_disclosures` は反芻思考の外在化のための筆記開示記録として機能します。
- `problem_solvings` は認知行動療法の問題解決法を実践するための記録です。
- `simple_notepads` は特定の心理療法に紐づかないシンプルなメモ帳機能です。
- `stressor_and_responses` はストレッサーとそれに対するストレス反応（認知・感情・身体・行動）を記録する機能です。
- `stress_person_encyclopedias` はストレスを感じる人物について整理・記録する機能です。
- `early_maladaptive_schemas` はスキーマ療法の18の早期不適応スキーマの囚われ度を記録する機能です。

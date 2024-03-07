-- このファイルはメモ用です。
-- new_database_base.sqlに[１.採番テーブル作成・初期化]のコードが含まれている

-- seq_reserve: 申請番号自動採番用テーブル
-- 1. 採番テーブル作成・初期化
CREATE TABLE seq_reserve (
	id INT NOT NULL, 
	y INT
);
INSERT INTO seq_reserve VALUES (0,YEAR(CURRENT_DATE));

-- 2. 採番実行/年越しリセット
UPDATE seq_reserve SET id=0,y=YEAR(CURRENT_DATE) WHERE NOT y=YEAR(CURRENT_DATE);
UPDATE seq_reserve SET id=LAST_INSERT_ID(id + 1);
SELECT LAST_INSERT_ID() as id;

-- 3. 全て初期化 
UPDATE seq_reserve set id=0, y=YEAR(CURRENT_DATE);

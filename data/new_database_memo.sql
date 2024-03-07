/*****自動採番の実現**********/
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


/****MRFU(most recently frequently used)順の実現******/

CREATE VIEW vw_mrfu AS 
SELECT apply_mid, instrument_id, MIN(DATEDIFF(CURDATE(),reserved)) as recency, COUNT(*) as freq
FROM vw_reserve 
GROUP BY apply_mid, instrument_id;

-- Inst-list Ordered by MRU 
SELECT i.*, IF(ISNULL(u.recency),365,u.recency) AS recency, freq  
FROM vw_instrument i LEFT JOIN vw_mrfu u ON i.id=u.instrument_id 
WHERE u.apply_mid=:member_id 
ORDER BY recency, room_id

-- Inst-list Ordered by MFU 
SELECT i.*, IF(ISNULL(u.recency),365,u.recency) AS recency, freq  
FROM vw_instrument i LEFT JOIN vw_mrfu u 
    ON (i.id=u.instrument_id AND u.apply_mid=:member_id) 
ORDER BY freq DESC, room_id

-- Inst-list ordered by MRFU 
SELECT i.*, IF(ISNULL(u.recency),365,u.recency) AS recency, freq 
FROM vw_instrument i LEFT JOIN vw_mrfu u 
    ON (i.id=u.instrument_id AND u.apply_mid=:member_id) 
ORDER BY recency, freq DESC, room_id

SELECT i.*, IF(ISNULL(u.recency),365,u.recency) AS recency, freq 
FROM vw_instrument i LEFT JOIN vw_mrfu u 
    ON (i.id=u.instrument_id AND u.apply_mid=:member_id) 
ORDER BY freq DESC, recency, room_id


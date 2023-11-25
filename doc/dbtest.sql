-- データベースの定義や操作に関するメモ（SQL文書き方のテスト等）
-- 1. SQL文コメントの書き方：
--    先頭2文字--に続いて半角スペース最低1文字
-- 2. 表名・列名で避けるべきキーワード(代わりの書き方prefix/suffix/abbr)
--    key, value, date, -> id, val, use_date(利用日),  
-- cf. https://en.wikipedia.org/wiki/List_of_SQL_reserved_words
-- 
-- 予約一覧および関連情報を調べるSQL
SELECT r.*, f.*, u.uname, u.urole, t.uid as master_uid , t.name as master_name 
    FROM tbl_facility f, tbl_reserve_test r, tbl_user u, tbl_teacher t 
    WHERE r.facility_id = f.id AND r.uid=u.uid AND t.uid=master_user
-- 
-- 学部・学科等コードのテーブル化（KsuCode->tbl_code）
CREATE TABLE tbl_code(
    id SERIAL PRIMARY KEY, -- 主キー。主に管理用（編集・削除）
    genre INT NOT NULL, -- 種類、複数種類のコードがあるため
    cd  VARCHAR(16) NOT NULL, -- コード
    val VARCHAR(32) NOT NULL, -- 値
    ref VARCHAR(16) -- 参照。上位コードへの参照　
);
-- 
INSERT INTO tbl_code(genre, cd, val, ref) VALUES
-- 
-- genre 一桁以内は組織や人に関すること
-- 
-- 学部  genre = 1 
(1, 'R', '理工学部', NULL),
(1, 'L', '生命科学部', NULL),
(1, 'U', '建築都市工学部', NULL),
(1, 'C', '商学部', NULL),
(1, 'E', '経済学部', NULL),
(1, 'D', '地域共創学部', NULL),
(1, 'A', '芸術学部', NULL),
(1, 'K', '国際文化学部', NULL),
(1, 'H', '人間科学部', NULL),

-- 学科  genre = 2  学籍番号パターン：/^\d{2}[A-Z]{2}\d{3}$/
(2, 'RS', '情報科学科', 'R'),
(2, 'RM', '機械工学科', 'R'),
(2, 'RE', '電気工学科', 'R'),        
(2, 'LL', '生命科学科', 'L'),        
(2, 'UA', '建築学科', 'U'),
(2, 'UH', '住居・インテリア学科', 'U'),
(2, 'UC', '都市デザイン工学科', 'U'),
(2, 'CB', '経営・流通学科', 'C'),
(2, 'EE', '経済学科', 'E' ),        
(2, 'DT', '観光学科', 'E'),
(2, 'DR', '地域づくり学科', 'D'),
(2, 'AA', '芸術表現学科', 'A'),
(2, 'AP', '写真・映像メディア学科', 'A'),
(2, 'AD', 'ビジュアルデザイン学科', 'A'),
(2, 'AE', '生活環境デザイン学科', 'A'),
(2, 'AS', 'ソーシャルデザイン学科', 'A'),        
(2, 'KK', '国際文化学科', 'K'),
(2, 'KN', '日本文化学科', 'K'),        
(2, 'HP', '臨床心理学科', 'H'),
(2, 'HC', '子ども教育学科', 'H'),
(2, 'HS', 'スポーツ学科', 'H'),

-- 大学院  genre = 3  学籍番号パターン：/^\d{2}[A-Z]{3}\d{2}$/
(3, 'GBE', '経済・ビジネス研究科 経済学専攻', NULL),
(3, 'GBM', '経済・ビジネス研究科 現代ビジネス専攻', NULL),
(3, 'GTI', '工学研究科 産業技術デザイン専攻', NULL),
(3, 'GJK', '情報科学研究科 情報科学専攻', NULL),
(3, 'GAC', '芸術研究科 造形表現専攻', NULL),
(3, 'GKK', '国際文化研究科 国際文化専攻', NULL),
(3, 'DBE', '経済・ビジネス研究科 経済学専攻', NULL),
(3, 'DBM', '経済・ビジネス研究科 現代ビジネス専攻', NULL),
(3, 'DTI', '工学研究科 産業技術デザイン専攻', NULL),
(3, 'DJK', '情報科学研究科 情報科学専攻', NULL),
(3, 'DAC', '芸術研究科 造形表現専攻', NULL),
(3, 'DKK', '国際文化研究科 国際文化専攻', NULL),
-- 
-- genre 二桁以降：人以外、主に物やことに関するコード
-- 
-- 申請状態 genre = 10
(10, '1', '申請中', NULL),
(10, '2', '審査中', NULL),
(10, '3', '承認済', NULL),
(10, '4', '却下済', NULL),

-- 学年歴関連 genre = 15, 16
(15, '1', '前学期', NULL),
(15, '2', '後学期', NULL),
     
(16, '1', '夏季休業', NULL),
(16, '2', '冬季休業', NULL),
(16, '3', '臨時休業', NULL),
(16, '4', '振替休業日', NULL),
 
-- 機器状態  genre = 20
(20, '1', '使用可', NULL),
(20, '2', '修理中', NULL),
-- 
-- 試料状態  genre = 30
(30, '1', '固体', NULL),
(30, '2', '液体', NULL),
(30, '3', '気体', NULL),
 
-- 試料特性  genre = 31
(31, '1', '爆発性', NULL),
(31, '2', '毒性', NULL),
(31, '3', '腐食性', NULL),
(21, '9', 'その他', NULL);

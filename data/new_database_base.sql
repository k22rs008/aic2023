/*********************************************
テーブル定義と基礎データ

<Person> Entities
. tb_user: ユーザ（ログイン可能なユーザ全員）
	. tb_memeber 会員（ユーザのうち予約権限のある学生・教職員）
		. tb_staff 教員（会員のうち責任者になれる教職員）

<Object> Entities
. tb_instrument: 機器
	. tb_room: 設置場所
	
<Thing> Relationships
. tb_reserve: 予約
	. rsv_sample: 試料特性
	. rsv_member: 利用代表者名簿
*********************************************/ 

-- tb_user: ユーザアカウントテーブル
-- 管理者アカウント以外は、LDAPによる認証を行いアカウント情報を自動登録

CREATE TABLE tb_user(
	uid VARCHAR(16) PRIMARY KEY COMMENT 'ログインID',
    urole INT NOT NULL COMMENT 'ユーザ種別(1:学生,2:教職員,9:管理者)',
    uname VARCHAR(16) NOT NULL COMMENT 'ユーザ名（表示名）',
    upass VARCHAR(32) COMMENT 'ログインパスワード',
    last_login DATETIME DEFAULT NULL COMMENT '直近ログイン時刻'
);

-- tb_member: 会員テーブル（学生テーブルと統合）
-- 基本情報はLDAPより取得、「会員編集」で追加変更可能

CREATE TABLE tb_member(
	id SERIAL PRIMARY KEY COMMENT '通し番号（自動採番, 内部参照用）',
    uid VARCHAR(16) NOT NULL UNIQUE COMMENT 'ユーザID',
	sid VARCHAR(16) NOT NULL UNIQUE COMMENT '学籍番号(文字列)・教職員番号(数字)',
	email VARCHAR(32) NOT NULL COMMENT 'メールアドレス',
	tel_no VARCHAR(32) COMMENT '電話番号',
	ja_name VARCHAR(32) NOT NULL COMMENT '日本語氏名',
	ja_yomi VARCHAR(32) COMMENT '日本語読み',
	en_name VARCHAR(32) COMMENT '英語氏名',
	en_yomi VARCHAR(32) COMMENT '英語読み',
	sex INT COMMENT '性別(0:未記入,1:男性,2:女性)',
	dept_name VARCHAR(64) COMMENT '所属名称, 例: 理工学部 情報科学科',
	dept_code VARCHAR(16) COMMENT '所属コード,例: RS',
	category INT COMMENT 'カテゴリ(1:一般学生,2:教育職員,3:事務職員,9:その他職員)',
	authority INT COMMENT '権限(1:予約権なし,2:予約付き)',
	granted TIMESTAMP COMMENT '権限付与・撤回日時',
	memo TEXT COMMENT '備考'
);

-- tb_staff: 教職員テーブル
-- 基本情報はLDAPより取得、「会員編集」で追加変更可能

CREATE TABLE tb_staff(
  id SERIAL PRIMARY KEY COMMENT '通し番号（自動採番, 内部参照用）',
  member_id INT NOT NULL COMMENT '',
  title VARCHAR(32)  COMMENT '役職1:大区分(大学教育職員,事務職員,職員)',
  rank VARCHAR(32)  COMMENT '役職2:中区分(教授,准教授,講師,助教,職員)',
  room_no VARCHAR(32) COMMENT '部屋番号',
  tel_ext VARCHAR(8) COMMENT '内線番号',
  responsible BOOLEAN COMMENT '責任者になれるか' 
);

-- tb_department: 部所テーブル

CREATE TABLE tb_department(
	id SERIAL PRIMARY KEY COMMENT '通し番号（自動採番, 内部参照用）',
	dept_code VARCHAR(16) UNIQUE COMMENT '部所コード,例: RS, AIC',
	dept_name VARCHAR(32) COMMENT '部所,例: 理工学部 情報科学科, 総合機器センター'
);

-- tb_reserve: 予約テーブル

CREATE TABLE tb_reserve(
	id SERIAL PRIMARY KEY COMMENT '通し番号（自動採番, 内部参照用）',
    instrument_id INT NOT NULL COMMENT '利用希望機器ID',
    apply_mid VARCHAR(16) NOT NULL COMMENT '申請者会員ID',
    master_mid VARCHAR(16) NOT NULL COMMENT '責任者会員ID',
    purpose VARCHAR(16) COMMENT '利用目的',
	other_user VARCHAR(64) COMMENT 'その他利用者',
	stime DATETIME NOT NULL COMMENT '利用開始日時',
    etime DATETIME NOT NULL COMMENT '利用終了日時',
	sample_name VARCHAR(64) NOT NULL COMMENT '試料名称',
	sample_state INT COMMENT '試料状態(1-個体,2-液体,3-気体)',
    xray_chk BOOLEAN COMMENT 'X線取扱者登録有無',
    xray_num VARCHAR(32) COMMENT 'X線取扱者登録者番号',
    status INT NOT NULL DEFAULT 1 COMMENT '申請状態(1:申請中,2:審査中,3:承認,4:却下)',
    memo TEXT COMMENT '備考',
    reserved DATETIME COMMENT '予約日',
	approved DATETIME COMMENT '承認日',
	lastmodified TIMESTAMP COMMENT '最終変更日'
);

-- rsv_member: 利用者名簿テーブル

CREATE TABLE rsv_member(
	id SERIAL PRIMARY KEY COMMENT '通し番号（自動採番, 内部参照用）',
	reserve_id INT NOT NULL COMMENT '予約番号',
	member_id  INT NOT NULL COMMENT '利用者会員番号',
	memo TEXT COMMENT '備考'
); 

-- rsv_sample: 試料情報テーブル
-- 特性：1:爆発性,2:毒性,3:腐食性,9:その他
CREATE TABLE rsv_sample(
	id SERIAL PRIMARY KEY COMMENT '通し番号（自動採番, 内部参照用）',
	reserve_id INT NOT NULL COMMENT '予約番号',
    nature INT NOT NULL COMMENT '試料特性値(1-爆発性,2-毒性,3-揮発性,4-その他)',
	other VARCHAR(16) COMMENT 'その他'
);

-- tb_room: 設置場所テーブル
CREATE TABLE tb_room(
	id SERIAL PRIMARY KEY COMMENT '通し番号（自動採番, 内部参照用）',
	room_name VARCHAR(32) NOT NULL COMMENT '部屋名称',
	room_no VARCHAR(16) COMMENT '部屋番号(略称)',
	memo TEXT COMMENT '備考'
);

-- tb_instrument: 機器設備テーブル

CREATE TABLE tb_instrument(
	id SERIAL PRIMARY KEY COMMENT '通し番号(自動採番, 内部参照用)',
	code VARCHAR(16) COMMENT '人間識別用番号',
	fullname VARCHAR(64) NOT NULL COMMENT '名称',
	shortname VARCHAR(64) NOT NULL COMMENT '略称', 
	state INT NOT NULL COMMENT '機器状態(1:使用可,2:貸出中,3:使用不可,9:その他)',
	category INT COMMENT 'カテゴリ（1:観察, 2:分析,3:計測,4:調製,9:その他）', 
	purpose VARCHAR(64) COMMENT '主な用途',
	detail TEXT COMMENT '施設紹介' ,
	maker  VARCHAR(64) COMMENT 'メーカー', 
	model VARCHAR(64) COMMENT '型式' ,
	made_year DATE COMMENT '製造年月' ,
	bought_year DATE COMMENT '導入年月' ,
	equipment_no VARCHAR(32) COMMENT '備品番号',
	room_id INT COMMENT '設置場所部屋番号', 
	memo TEXT  COMMENT '備考'
);

--　ユーザアカウント情報
INSERT INTO tb_user
(uid, urole, uname, upass) VALUES
('admin', 9, '管理者', '4321');

INSERT INTO tb_department
(dept_code, dept_name) VALUES
('RS','理工学部 情報科学科'),
('RM','理工学部 機械工学科'),
('RE','理工学部 電気工学科'),
('LL','生命科学部 生命科学科'),
('UA','建築都市工学部 建築学科'),
('UH','建築都市工学部 住居・インテリア学科'),
('UC','建築都市工学部 都市デザイン工学科'),
('CB','商学部 経営・流通学科'),
('EE','経済学部 経済学科'),
('DT','地域共創学部 観光学科'),
('DR','地域共創学部 地域づくり学科'),
('AA','芸術学部 芸術表現学科'),
('AP','芸術学部 写真・映像メディア学科'),
('AD','芸術学部 ビジュアルデザイン学科'),
('AE','芸術学部 生活環境デザイン学科'),
('AS','芸術学部 ソーシャルデザイン学科'),
('KK','国際文化学部 国際文化学科'),
('KN','国際文化学部 日本文化学科'),
('HP','人間科学部 臨床心理学科'),
('HC','人間科学部 子ども教育学科'),
('HS','人間科学部 スポーツ学科'),

('GBE','経済・ビジネス研究科 経済学専攻・博士前期課程'),
('GBM','経済・ビジネス研究科 現代ビジネス専攻・博士前期課程'),
('GTI','工学研究科 産業技術デザイン専攻・博士前期課程'),
('GJK','情報科学研究科 情報科学専攻・博士前期課程'),
('GAC','芸術研究科 造形表現専攻・博士前期課程'),
('GKK','国際文化研究科 国際文化専攻・博士前期課程'),

('DBE','経済・ビジネス研究科 経済学専攻・博士後期課程'),
('DBM','経済・ビジネス研究科 現代ビジネス専攻・博士後期課程'),
('DTI','工学研究科 産業技術デザイン専攻・博士後期課程'),
('DJK','情報科学研究科 情報科学専攻・博士後期課程'),
('DAC','芸術研究科 造形表現専攻・博士後期課程'),
('DKK','国際文化研究科 国際文化専攻・博士後期課程'),

('AIC','総合機器センター'),
('CNC','総合情報基盤センター'),
('KKC','基礎教育センター'),
('GKC','語学教育研究センター'),
('SGK','産学連携支援室'),
-- 架空の学部学科
('LT','生体医工学部 生体工学科'),
('GLT','生体医工学研究科 生体工学専攻・博士前期課程'),
('DLT','生体医工学研究科 生体工学科・博士後期課程');

-- 設置場所情報
-- 部屋名,部屋番号
INSERT INTO tb_room 
(room_name, room_no) VALUES
('X線光電子分析室', '①'), -- 1
('微小部分析型走査顕微鏡室', '②'), -- 2
('元素分析室', '③'), -- 3
('X線回折測定室', '④'), -- 4
('ＬC-Mass測定室', '⑤'), -- 5
('蛍光X線測定室', '⑥'), -- 6
('IR測定室', '⑦'), -- 7
('原子吸光分析室', '⑦'), -- 8
('GC-Mass測定室', '⑧'), -- 9
('分光分析室', '⑨'), -- 10
('磁気物性測定室', '⑩'), -- 11
('7号館１階分子生命工学ゼミナール室', '⑪'), -- 12
('10号館１階ロボット実験室', NULL), -- 13
('8号館1階電子物性実験室', NULL), -- 14
('倉庫', NULL); -- 15

-- 機器設備情報（NEW）：「設置場所テーブル」と連携する場合
-- コード.,機器名,略称,状態,カテゴリ,設置場所,備考
-- 状　　態(state) 1:使用可,2:貸出中,3:使用不可
-- カテゴリ(category) 1:観察, 2:分析,3:計測,4:調製,9:その他
INSERT INTO tb_instrument
(code,fullname,shortname,state,category,room_id,memo) VALUES
(1,'３Ｄデジタルファインスコープ','３ＤＳ',1,1,7, null),
(2,'原子吸光分析システム','ＡＡ',1,2,8, null),
(3,'ビジネスプロジェクター','ＢＰ',3,9,15, null),
(4,'円二色性分散計 高速ストップドフローシステム','ＣＤ',1,2,10, null),
(5,'キュリーポイントインジェクター','ＣＰＩ',1,4,9, null),
(6,'示差走査熱量計','ＤＳＣ',1,2,9, null),
(7,'元素分析装置','ＥＡ',1,2,3, null),
(8,'蛍光発光マイクロプレートリーダ','ＦＬ',1,2,10, null),
(9,'蛍光分光光度計','ＦＳＰ',1,2,7, null),
(10,'フーリエ変換赤外分光光度計','ＦＴ-ＩＲ',1,2,7, null),
(11,'放射線測定器','ＧＡＭＭＡ',1,3,11, null),
(12,'ガスクロマトグラフ質量分析計','ＧＣ-ＭＳ',1,2,9, null),
(13,'高速液体クロマトグラフ','HPLC',1,2,5, null),
(14,'イオンクロマトグラフ','ＩＣ',1,2,8, null),
(15,'ＩＣＰ発光分光分析装置','ＩＣＰ',1,2,6, null),
(16,'文化財赤外紫外線撮影装置','IRUV-camera',1,1,10, null),
(17,'マルチ高速液体クロマトグラフシステム','ＬＣＭＳ',1,2,5, null),
(18,'ＬＣＲメータ','ＬＣＲ',1,3,10, null),
(19,'レーザー線幅測定器','ＬＬＷ',1,3,11, null),
(20,'環境情報測定解析システム','ＬＭＳ',2,3,10,'牛見先生貸出中'),
(21,'マトリックス支援レーザー脱離イオン化飛行時間型質量分析計','ＭＡＬＤＩ',1,2,9, null),
(22,'卓上電子顕微鏡','mini-ＳＥＭ',1,1,2, null),
(23,'ナノ材料高分解能磁気測定システム','ＭＰＭＳ３',1,3,14, null),
(24,'筋骨格系専用立体MRIイメージング装置','MRI',1,3,13, null),
(25,'リアルタイムPCRシステム','ＰＣＲ',1,2,7,null),
(26,'粉体大気圧プラズマ処理装置','Ｐｌａｍｉｎｏ',1,4,9, null),
(27,'極低温高磁場物性測定システム','ＰＰＭＳ',3,3,11, '故障中'),
(28,'多目的高分解能走査電子顕微鏡システム','SEM',1,1,2, null),
(29,'核磁気共鳴装置','Ｓ-ＮＭＲ',1,2,12, null),
(30,'走査型プローブ顕微鏡','ＳＰＭ',1,1,7, null),
(31,'ＴＧ/ＤＴＡガストランスファー分析システム','ＴＧ/ＤＴＡ',1,2,9, null),
(32,'ＴＧ-ＤＳＣ-ＭＳ-ＦＴＩＲ同時熱分析装置','ＴＧＭＳＩＲ',1,2,9, null),
(33,'自動比表面積/細孔分布測定装置','ＴＲＩＳＴＡＲ',1,3,10, null),
(34,'紫外可視近赤外分光光度計','ＵＶ',1,3,10, null),
(35,'高真空蒸着装置','VE',1,4,8, null),
(36,'Ｘ線光電子分析装置','ＸＰＳ',1,2,1, null),
(37,'多目的X線回折測定システム Mini Flex','ＸＲＤ',1,3,4, null),
(38,'多目的X線回折測定システム Smart Lab','ＸＲＤ',1,3,4, null),
(39,'高速大容量冷却遠心機','遠心分離機',1,4,11, null),
(40,'電離箱式サーベイメータ','サーベイメータ',3,3,15, null),
(41,'赤外線サーモグラフカメラ','サーモグラフィー',3,3,15, null),
(42,'純水製造装置','蒸留水',1,4,9, null),
(43,'超純水製造システム','超純水',1,4,8, null),
(44,'超純水製造システム','超純水',1,4,8, null),
(45,'ウルトラミクロ天秤','マイクロ天秤',1,3,7, null),
(46,'粘度粘弾性測定装置','レオメータ',1,3,10, null);

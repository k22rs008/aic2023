-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2023-11-24 08:05:55
-- サーバのバージョン： 10.4.25-MariaDB
-- PHP のバージョン: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `aic_test`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_facility`
--

CREATE TABLE `tbl_facility` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fname` varchar(64) NOT NULL,
  `fshortname` varchar(16) NOT NULL,
  `maker` varchar(64) DEFAULT NULL,
  `iyear` date DEFAULT NULL,
  `splace` varchar(16) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `tbl_facility`
--

INSERT INTO `tbl_facility` (`id`, `fname`, `fshortname`, `maker`, `iyear`, `splace`, `purpose`, `note`) VALUES
(1, '走査型電子顕微鏡', 'SEM', '日本電子　JSM-6060', '2017-09-15', '14034', '固体試料の表面形態観察', '加速電圧5～30kV、分解能3.5nm、倍率は8～30万倍である。操作はパソコン（OS Windows XP）で行い、データは、利用者で用意したCD、メモリースティック、外付けハードディスク等に保存する。写真撮影機能はない。試料導入口が大きいため、大きな試料も観察することができる。試料台は、10mmφと32mmφが使用できる。冷却水循環装置（EYELA CA-1112）を備えている。\r\n\r\n（付）イオンコーター（エイコーエンジニアリング製　IB-2型）走査型電顕用試料の表面を導体化するために、金をコーティングする装置である。生物、高分子材料等のイオンエッチングも行える機能をもつ。'),
(2, '高精細デジタルマイクロスコープ', 'VHX', 'キーエンス VH-6300', '2015-07-12', '14012', '光学式で3000倍までのデジタル拡大画像を作成', '25～175倍と、450～3000倍の2種類のズームレンズがあり、光学式で、最大3000倍の明るい像が得られる。高倍率では、反射と透過の両方の像が得られる。90万画素のCCDを使用。長さ、面積、角度などの計測機能を持つ。3.5インチのフロッピーディスクに保存できる。'),
(3, 'X線光電子分析装置', 'ESC', '島津ESCA-3400', '2020-01-19', '14052', '固体最表面（深さ数nm）の元素分析、化学結合状態を分析する', '軟X線照射によって、固体表面から放出された光電子の結合エネルギーを測定する装置であり、固体最表面（深さ数ナノメートル）の元素分析と、酸化の状態や有機物の官能基の種類を知ることができる。\r\n\r\nX線銃は、Mg/Alデュアルアノードである。10試料一括導入キット付属。高速イオン銃を備えているので、試料の表面をイオンエッチングで削り、深さ方向の分析ができる。\r\n\r\n試料サイズは、最大10ｍｍφで、高さは5ｍｍ以内である。粉末試料ホルダーを3個備えている。測定データはフラッシュメモリーなどに保存できる。'),
(4, '極低温高磁場物性測定システム', 'PPH', 'カンタムデザインPPHC14', '2015-12-21', '14004', '1.9Kの極低温から、14ﾃｽﾗの高磁場下での、比熱、電気抵抗、磁気抵抗、磁化率等の物性測定', '（構成）\r\n14テスラ可変温度比熱測定システム\r\nPPHC14\r\n\r\n抵抗測定オプション、AC磁化率/DC帯磁率測定オプション、高分解能水平サンプルローテーターアッセンブリ、トルクマグネットメーター、ACトランスポート測定システムを付属。\r\n\r\n　本装置は、極低温の1.9Kから室温の範囲で、14テスラまでの高磁界を発生できる。超伝導体の臨界電流密度、比熱、電気抵抗、ホール効果、磁気抵抗、磁化率等の諸物理量を広範囲の温度、磁界下で測定できる。\r\n\r\n液体ヘリウムと液体窒素は利用者で用意する。ただし、年1回、装置立ち上げ時に必要な液体ヘリウムと液体窒素はセンターが用意する。'),
(5, '示差走査熱量計', 'DSC', 'リガク　DSC8230', '2009-10-29', '14001', '各種材料の加熱時における分解性、反応性の解析 比熱、反応熱、転移熱の定量', '試料（個体、液体）の吸熱量、発熱量測定から融解、熱分解、相転移挙動を調べることができる。試料は、専用セル（Al）に数mgを入れて、クリンパで密封する。測定温度範囲は、-150℃〜725℃、プログラム速度は、1℃/h〜100℃/minである。解析ソフトを使用することにより、データ（アスキー変換、エクセル）をフラシュメモリーに保存できる。');

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_fd`
--

CREATE TABLE `tbl_fd` (
  `fdid` varchar(6) NOT NULL,
  `faculty` varchar(16) NOT NULL,
  `fdname` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_reserve`
--

CREATE TABLE `tbl_reserve` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `facility_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `uid` varchar(14) NOT NULL,
  `others` text DEFAULT NULL,
  `reserved` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stime` timestamp NOT NULL DEFAULT current_timestamp(),
  `etime` timestamp NOT NULL DEFAULT current_timestamp(),
  `xraychk` int(11) DEFAULT NULL,
  `xraynum` varchar(32) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `decided` int(11) DEFAULT NULL,
  `purpose` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `tbl_reserve`
--

INSERT INTO `tbl_reserve` (`id`, `facility_id`, `sample_id`, `uid`, `others`, `reserved`, `stime`, `etime`, `xraychk`, `xraynum`, `note`, `decided`, `purpose`) VALUES
(1, 1, 1, 'akane', '', '2023-09-08 01:24:00', '2023-09-10 01:05:00', '2023-09-10 03:32:00', 0, '', '', 0, '卒論'),
(2, 1, 2, 'daiki', '', '2023-09-07 08:20:00', '2023-09-08 00:03:00', '2023-09-08 02:39:00', 1, '63928', '', 0, '講義'),
(3, 2, 2, 'seiji', '○○高校見学　○名参加', '2023-09-03 03:10:00', '2023-09-05 04:08:00', '2023-09-05 07:22:00', 0, '', '', 1, 'オープンキャンパス'),
(4, 5, 4, 'k21ll003', '', '2023-09-03 05:17:00', '2023-09-06 11:59:00', '2023-09-06 13:09:00', 0, '', '時間外利用', 2, '卒論');

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_reserve_test`
--

CREATE TABLE `tbl_reserve_test` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `facility_id` int(11) NOT NULL,
  `uid` varchar(16) NOT NULL,
  `master_user` varchar(16) NOT NULL,
  `other` text DEFAULT NULL,
  `reserved` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stime` timestamp NOT NULL DEFAULT current_timestamp(),
  `etime` timestamp NOT NULL DEFAULT current_timestamp(),
  `xraychk` int(11) DEFAULT NULL,
  `xraynum` varchar(32) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `decided` int(11) DEFAULT NULL,
  `purpose` varchar(16) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `tbl_reserve_test`
--

INSERT INTO `tbl_reserve_test` (`id`, `facility_id`, `uid`, `master_user`, `other`, `reserved`, `stime`, `etime`, `xraychk`, `xraynum`, `note`, `decided`, `purpose`, `comment`) VALUES
(1, 1, 'akane', 'akane', '', '2023-11-24 00:32:03', '2023-11-24 01:05:00', '2023-11-24 03:32:00', 0, '', '', 0, '卒論', ''),
(2, 1, 'daiki', 'daiki', '', '2023-09-07 08:20:00', '2023-09-08 00:03:00', '2023-09-08 02:39:00', 1, '63928', '', 0, '講義', ''),
(3, 2, 'seiji', 'seiji', '○○高校見学　○名参加', '2023-11-24 00:30:31', '2023-11-24 04:08:00', '2023-11-24 07:22:00', 0, '', '', 1, 'オープンキャンパス', '正しく利用しましょう'),
(4, 5, 'k21ll003', 'mai', '', '2023-09-03 05:17:00', '2023-09-06 11:59:00', '2023-09-06 13:09:00', 0, '', '時間外利用', 2, '卒論', ''),
(5, 2, 'k21ll001', 'tubasa', '', '2023-11-24 00:31:26', '2023-11-23 20:00:00', '2023-11-24 01:00:00', 0, '', '', 1, '', ''),
(6, 4, 'k21ll001', 'tubasa', '', '2023-11-24 00:35:28', '2023-11-23 15:00:00', '2023-11-24 14:55:00', 0, '', '', 2, '', ''),
(7, 1, 'k21ll001', 'tubasa', '', '2023-11-02 02:51:11', '2023-11-01 20:00:00', '2023-11-02 04:00:00', 0, '', '', 1, '', ''),
(8, 1, 'k21ll001', 'tubasa', '', '2023-11-02 02:51:59', '2023-11-03 20:00:00', '2023-11-04 07:00:00', 0, '', '', 2, '', ''),
(9, 1, 'k21ll001', 'tubasa', '', '2023-11-02 09:14:53', '2023-11-02 05:00:00', '2023-11-02 08:00:00', 0, '', '', 2, '', ''),
(10, 2, 'k21ll001', 'tubasa', '', '2023-11-02 09:16:22', '2023-11-01 20:00:00', '2023-11-02 08:00:00', 0, '', '', 1, '', ''),
(11, 1, 'k21ll001', 'tubasa', '', '2023-11-02 09:31:22', '2023-11-02 05:00:00', '2023-11-02 07:00:00', 0, '', '', 2, '', ''),
(12, 1, 'k21ll001', 'tubasa', '', '2023-11-02 13:13:40', '2023-11-01 20:00:00', '2023-11-02 03:00:00', 0, '', '', 2, '', ''),
(13, 1, 'k21ll001', 'tubasa', '', '2023-11-02 14:25:00', '2023-11-01 20:00:00', '2023-11-02 03:00:00', 0, '', '', 2, '', ''),
(14, 1, 'k21ll001', 'tubasa', '', '2023-11-03 01:08:05', '2023-11-02 20:00:00', '2023-11-03 02:00:00', 0, '', '', 2, '', ''),
(15, 2, 'k21ll001', 'tubasa', '', '2023-11-03 01:16:40', '2023-11-03 03:00:00', '2023-11-03 05:00:00', 0, '', '', 2, '', ''),
(16, 3, 'k21re005', 'tubasa', '', '2023-11-03 01:59:48', '2023-11-02 20:00:00', '2023-11-03 01:00:00', 0, '', '', 2, '', ''),
(17, 4, 'k21ll001', 'tubasa', '', '2023-11-03 03:53:30', '2023-11-03 02:00:00', '2023-11-03 04:00:00', 0, '', '', 2, '', ''),
(18, 5, 'k21ll001', 'tubasa', '', '2023-11-03 04:52:47', '2023-11-03 00:00:00', '2023-11-03 03:00:00', 0, '', '', 2, '', ''),
(19, 1, 'k21ll001', 'tubasa', '', '2023-11-03 05:24:45', '2023-11-03 08:00:00', '2023-11-03 11:00:00', 0, '', '', 2, '', ''),
(20, 1, 'k21ll003', 'tubasa', '', '2023-11-03 06:04:20', '2023-11-03 03:00:00', '2023-11-03 05:00:00', 0, '', '', 2, '', ''),
(21, 3, 'k21ll002', 'tubasa', '', '2023-11-03 06:54:54', '2023-11-03 05:00:00', '2023-11-03 08:00:00', 0, '', '', 2, '', ''),
(22, 1, 'k21ll001', 'tubasa', '', '2023-11-05 08:22:00', '2023-11-02 20:00:00', '2023-11-02 22:00:00', 0, '', '', 1, '', ''),
(23, 1, 'k21ll001', 'tubasa', '', '2023-11-06 02:43:22', '2023-11-02 20:00:00', '2023-11-03 03:00:00', 0, '', '', 2, '', ''),
(24, 1, 'k21ll001', 'tubasa', '', '2023-11-08 08:18:47', '2023-11-07 22:00:00', '2023-11-08 01:00:00', 0, '', '', 2, '', ''),
(25, 5, 'k21ll001', 'tubasa', '', '2023-11-09 09:13:11', '2023-11-08 20:00:00', '2023-11-09 06:00:00', 0, '', '', 1, '', ''),
(26, 1, 'k21ll001', 'tubasa', '', '2023-11-24 00:32:32', '2023-11-24 05:39:00', '2023-11-24 09:42:00', 0, '', '', 1, '', ''),
(27, 2, 'k21ll001', 'tubasa', '', '2023-11-24 06:45:47', '2023-11-26 23:30:00', '2023-11-27 09:45:00', 0, '', '', 0, '', ''),
(28, 3, 'k21ll001', 'daiki', '', '2023-11-24 06:59:02', '2023-11-30 01:00:00', '2023-11-30 09:00:00', 0, '', '', 2, '', '');

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_reserve_user`
--

CREATE TABLE `tbl_reserve_user` (
  `reserve_id` int(11) NOT NULL,
  `reserve_user` varchar(16) NOT NULL,
  `urole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `tbl_reserve_user`
--

INSERT INTO `tbl_reserve_user` (`reserve_id`, `reserve_user`, `urole`) VALUES
(1, 'akane', 5),
(1, 'k21ll001', 1),
(1, 'k21ll003', 1),
(1, 'k21ll004', 1),
(1, 'k21re008', 1),
(2, 'daiki', 5),
(2, 'seiji', 5),
(2, 'k21re005', 1),
(2, 'k21re007', 1),
(2, 'k21re009', 1),
(3, 'seiji', 5),
(3, 'k21ll001', 1),
(3, 'k21ll002', 1),
(4, 'k21ll003', 1),
(4, 'daiki', 5),
(4, 'tubasa', 5),
(4, 'k21re007', 1),
(4, 'k21re010', 1),
(5, 'tubasa', 5),
(5, 'k21ll001', 1),
(6, 'tubasa', 5),
(6, 'k21ll001', 1),
(7, 'tubasa', 5),
(7, 'k21ll001', 1),
(8, 'tubasa', 5),
(8, 'k21ll001', 1),
(9, 'tubasa', 5),
(9, 'k21ll001', 1),
(10, 'tubasa', 5),
(10, 'k21ll001', 1),
(11, 'tubasa', 5),
(11, 'k21ll001', 1),
(12, 'tubasa', 5),
(12, 'k21ll001', 1),
(13, 'tubasa', 5),
(13, 'k21ll001', 1),
(14, 'tubasa', 5),
(14, 'k21ll001', 1),
(14, 'k21ll002', 1),
(15, 'tubasa', 5),
(15, 'k21ll001', 1),
(15, 'k21ll002', 1),
(16, 'tubasa', 5),
(16, 'k21re005', 1),
(16, 'k21ll001', 1),
(16, 'k21re006', 1),
(17, 'tubasa', 5),
(17, 'k21ll001', 1),
(18, 'tubasa', 5),
(18, 'k21ll001', 1),
(19, 'tubasa', 5),
(19, 'k21ll001', 1),
(20, 'tubasa', 5),
(20, 'k21ll003', 1),
(21, 'tubasa', 5),
(21, 'k21ll002', 1),
(21, 'k21ll001', 1),
(22, 'tubasa', 5),
(22, 'k21ll001', 1),
(23, 'tubasa', 5),
(23, 'k21ll001', 1),
(23, 'k21ll003', 1),
(24, 'tubasa', 5),
(24, 'k21ll001', 1),
(24, 'k21ll003', 1),
(25, 'tubasa', 5),
(25, 'k21ll001', 1),
(25, 'k21ll003', 1),
(26, 'tubasa', 5),
(26, 'k21ll001', 1),
(26, 'k21ll002', 1),
(27, 'tubasa', 5),
(27, 'k21ll002', 1),
(28, 'daiki', 5),
(28, 'k21ll004', 1),
(28, 'k21ll003', 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_sample`
--

CREATE TABLE `tbl_sample` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `saname` varchar(32) NOT NULL,
  `sastate` int(11) DEFAULT NULL,
  `sachara` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `tbl_sample`
--

INSERT INTO `tbl_sample` (`id`, `saname`, `sastate`, `sachara`) VALUES
(1, 'kafdhuo(適当)', 1, '爆発性'),
(2, 'akjfdoaye(適当)', 2, '毒性'),
(3, 'lifvuhsds(適当)', 3, '腐食性'),
(4, 'qihuzns(適当)', 1, '毒性');

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_sample_test`
--

CREATE TABLE `tbl_sample_test` (
  `reserve_id` int(11) NOT NULL,
  `saname` varchar(32) NOT NULL,
  `sastate` int(11) DEFAULT NULL,
  `sachara` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `tbl_sample_test`
--

INSERT INTO `tbl_sample_test` (`reserve_id`, `saname`, `sastate`, `sachara`) VALUES
(5, '', 1, '爆発性'),
(6, '', 1, '爆発性'),
(7, '', 1, '爆発性'),
(8, '', 1, '爆発性'),
(9, '', 1, '爆発性'),
(10, '', 1, '爆発性'),
(11, '', 1, '爆発性'),
(12, '', 1, '爆発性'),
(13, '', 1, '爆発性'),
(14, '', 1, '爆発性'),
(15, '', 1, '爆発性'),
(16, '', 1, '爆発性'),
(17, '', 1, '爆発性'),
(18, '', 1, '爆発性'),
(19, '', 1, '爆発性'),
(20, '試料A', 3, '爆発性'),
(21, '', 1, '爆発性'),
(22, '', 1, '爆発性'),
(23, '', 1, '爆発性'),
(24, '', 1, '爆発性'),
(25, '', 1, '爆発性'),
(26, 'sss', 1, '爆発性'),
(27, 'sss', 1, '爆発性'),
(28, '', 1, '爆発性');

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_student`
--

CREATE TABLE `tbl_student` (
  `stid` char(7) NOT NULL,
  `uid` varchar(14) NOT NULL,
  `fdid` varchar(6) NOT NULL,
  `name` varchar(16) NOT NULL,
  `sex` int(11) DEFAULT NULL,
  `tel` varchar(26) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `tbl_student`
--

INSERT INTO `tbl_student` (`stid`, `uid`, `fdid`, `name`, `sex`, `tel`) VALUES
('21LL001', 'k21ll001', 'LL', '横山悠菜', 2, '080-5319-6888'),
('21LL002', 'k21ll002', 'LL', '澤田瑞希', 2, '080-8286-6729'),
('21LL003', 'k21ll003', 'LL', '田辺咲羽', 2, '080-3506-8079'),
('21LL004', 'k21ll004', 'LL', '吉村樹', 1, '070-3683-7271'),
('21RE005', 'k21re005', 'RE', '河野惇', 1, '070-7389-3585'),
('21RE006', 'k21re006', 'RE', '矢野慶次', 1, '070-8854-4326'),
('21RE007', 'k21re007', 'RE', '平井雄飛', 1, '070-4673-7971'),
('21RE008', 'k21re008', 'RE', '近藤優華', 2, '090-1742-9651'),
('21RE009', 'k21re009', 'RE', '岡田哲也', 1, '090-3598-8445'),
('21RE010', 'k21re010', 'RE', '金子雄太', 1, '080-4536-1675');

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_teacher`
--

CREATE TABLE `tbl_teacher` (
  `tid` varchar(14) NOT NULL,
  `uid` varchar(14) NOT NULL,
  `fdid` varchar(6) NOT NULL,
  `name` varchar(16) NOT NULL,
  `sex` int(11) DEFAULT NULL,
  `tel` varchar(26) DEFAULT NULL,
  `trole` int(11) DEFAULT NULL,
  `room` varchar(16) DEFAULT NULL,
  `sfield` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `tbl_teacher`
--

INSERT INTO `tbl_teacher` (`tid`, `uid`, `fdid`, `name`, `sex`, `tel`, `trole`, `room`, `sfield`) VALUES
('akane', 'akane', 'LL', '伊東茜', 2, '070-9577-3505', 2, '19502', '海洋分子生物学'),
('daiki', 'daiki', 'RE', '石橋大樹', 1, '080-0094-5882', 2, '18612', '電気磁気学'),
('mai', 'mai', 'LL', '遠藤舞', 2, '080-9591-3792', 3, '19201', '情報生理学'),
('seiji', 'seiji', 'RE', '安部誠司', 1, '080-4895-2627', 1, '18603', '電子回路の設計'),
('tubasa', 'tubasa', 'LL', '大橋翼', 1, '090-5540-0862', 1, '19611', '細胞生物学');

-- --------------------------------------------------------

--
-- テーブルの構造 `tbl_user`
--

CREATE TABLE `tbl_user` (
  `uid` varchar(14) NOT NULL,
  `urole` int(11) NOT NULL,
  `uname` varchar(16) NOT NULL,
  `upass` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `tbl_user`
--

INSERT INTO `tbl_user` (`uid`, `urole`, `uname`, `upass`) VALUES
('admin', 9, '管理者', '5678'),
('akane', 5, '伊東茜', '3456'),
('daiki', 5, '石橋大樹', '3456'),
('k21ll001', 1, '横山悠菜', '1234'),
('k21ll002', 1, '澤田瑞希', '1234'),
('k21ll003', 1, '田辺咲羽', '1234'),
('k21ll004', 1, '吉村樹', '1234'),
('k21re005', 1, '河野惇', '1234'),
('k21re006', 1, '矢野慶次', '1234'),
('k21re007', 1, '平井雄飛', '1234'),
('k21re008', 1, '近藤優華', '1234'),
('k21re009', 1, '岡田哲也', '1234'),
('k21re010', 1, '金子雄太', '1234'),
('mai', 5, '遠藤舞', '3456'),
('seiji', 5, '安部誠司', '3456'),
('tubasa', 5, '大橋翼', '3456');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `tbl_facility`
--
ALTER TABLE `tbl_facility`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- テーブルのインデックス `tbl_fd`
--
ALTER TABLE `tbl_fd`
  ADD PRIMARY KEY (`fdid`);

--
-- テーブルのインデックス `tbl_reserve`
--
ALTER TABLE `tbl_reserve`
  ADD UNIQUE KEY `id` (`id`);

--
-- テーブルのインデックス `tbl_reserve_test`
--
ALTER TABLE `tbl_reserve_test`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- テーブルのインデックス `tbl_sample`
--
ALTER TABLE `tbl_sample`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- テーブルのインデックス `tbl_student`
--
ALTER TABLE `tbl_student`
  ADD PRIMARY KEY (`stid`);

--
-- テーブルのインデックス `tbl_teacher`
--
ALTER TABLE `tbl_teacher`
  ADD PRIMARY KEY (`tid`);

--
-- テーブルのインデックス `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`uid`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `tbl_facility`
--
ALTER TABLE `tbl_facility`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `tbl_reserve`
--
ALTER TABLE `tbl_reserve`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `tbl_reserve_test`
--
ALTER TABLE `tbl_reserve_test`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- テーブルの AUTO_INCREMENT `tbl_sample`
--
ALTER TABLE `tbl_sample`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

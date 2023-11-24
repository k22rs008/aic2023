-- 予約一覧および関連情報を調べる
SELECT r.*, f.*, u.uname, u.urole, t.uid as master_uid , t.name as master_name 
    FROM tbl_facility f, tbl_reserve_test r, tbl_user u, tbl_teacher t 
    WHERE r.facility_id = f.id AND r.uid=u.uid AND t.uid=master_user
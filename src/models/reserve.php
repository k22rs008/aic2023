<?php
// namespace ksu/aic;

require_once('Model.php');

class Reserve extends Model{
    protected $table = "tb_reserve";
    protected $inst_table = 'tb_instrument';
    protected $member_table = 'tb_member';

    const status = [1=>'申請中', 2=>'審査中', 3=>'承認済', 9=>'拒否'];
    const style = [1=>'red', 2=>'green', 3=>'blue', 9=>'black'];
    const sample_property = [1=>'爆発性',2=>'毒性',3=>'腐食性', 9=>'その他'];
    const sample_state = [1=>'気体',2=>'液体',3=>'固体'];
    
    function getDetail($id)
    {
        $rsv = parent::getDetail($id);
        if ($rsv) return false;
        // $apply_user = (new User)->getDetail($rsv['uid']);
        // $master_user = (new Staff)->getDetail($rsv['master_user']);
    }

    function getListDetail($fid=0, $status=9)
    {
        global $conn;        

        $sql = "SELECT r.*, f.fullname, f.fshortname,m1.ja_name AS apply_name, m2.ja_name AS master_name
          FROM %s r, %s f, %s m1, %s m2 WHERE r.mid=m1.mid AND r.master_mid=m2.mid AND f.id=r.instrument_id ";
        $sql = sprintf($sql, $this->table, $this->inst_table, $this->member_table, $this->member_table);
        if ($fid){ // $fid= 0 for all, or 1~ for one specific instrument 
            $sql .= " AND r.instrument_id=$fid"; 
        }
        if ($status < 9){ // $status=9 for all, or 1~ for one specific status
            $sql .= " AND r.status=$status"; 
        }
        $sql .= ' ORDER BY instrument_id, stime, etime';
        
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * $fid: int, , instrument id, 0 for all instrument
     */
    function getListByFid($fid=0, $date1=null, $date2=null)
    {
        global $conn;
        $sql = "SELECT r.*, m.ja_name as master_name FROM %s r, %s m WHERE r.master_mid=m.id";
        $sql = sprintf($sql, $this->table, $this->member_table);
        if ($fid){
            $sql .= " AND r.instrument_id=$fid"; 
        }
        if ($date1 and $date2){
            $sql .= " AND GREATEST(stime, '{$date1} 00:00') <= LEAST(etime, '{$date2} 23:59')"; 
        }elseif($date1 and !$date2){
            $sql .= " AND etime > '{$date1}'";
        }

        $sql .= ' ORDER BY instrument_id, stime, etime';
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }
  
    function getItems($fid, $date1=null, $date2=null)
    {
        $rows = $this->getListByFid($fid, $date1, $date2);
        return self::toItems($rows);
    }

    static function toItems($rows)
    {
        $items = [];
        foreach ($rows as $row){
            $e = $row['status'];
            $items[] = [
              'id' => $row['id'],
              'group'=>$row['instrument_id'],
              'title'=>$row['purpose'] .'（'. self::status[$e] . '）'. $row['master_name'],
              'className'=> isset(self::style[$e]) ? self::style[$e] : 'black', 
              'start'=> $row['stime'],
              'end'=> $row['etime'],
            ];
        }
        return $items;
    }

}
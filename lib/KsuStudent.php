<?php
namespace kst;

include_once 'KsuCode.php';
use kst\KsuCode;

class KsuStudent{

    public static function parseSid($str){
        $sid = self::validateSid($str);
        if (!$sid) return null;//正しい学生番号ではない
        if (preg_match('/^\d{2}[A-Z]{2}\d{3}$/', $sid)){
            $stud_yr = substr($sid, 0, 2);
            $dept_id = substr($sid, 2, 2);
            $stud_no = substr($sid, -3);
            if (in_array($dept_id, array_keys(KsuCode::FACULTY_DEPT))){
                $dept = KsuCode::FACULTY_DEPT[$dept_id];
            }
        }
        if (preg_match('/^\d{2}[A-Z]{3}\d{2}$/', $sid)){
            $stud_yr = substr($sid, 0, 2);
            $dept_id = substr($sid, 2, 3);
            $stud_no = substr($sid, -2);
            if (in_array($dept_id, array_keys(KsuCode::GRADUATE_SCHL))){
                $dept = KsuCode::GRADUATE_SCHL[$dept_id];
            }
        }
        if (isset($sid, $stud_yr, $dept_id, $stud_no, $dept)){
            list ($faculty_name, $dept_name) = explode(' ', $dept);
            return [$sid, $stud_yr, $dept_id, $stud_no, $faculty_name, $dept_name];
        } 
        return null;
    }

    /** 文字列が学籍番号の標準形に変換可能かをチェックし，可能なら正しい学籍番号を返す
     * 全角英数を半角に変換、スペースや改行コードを削除、小文字を大文字に変換する */
    public static function validateSid($str)
    {
        $str = preg_replace("/( |　)/", "", trim($str) );//空白文字を削除
        if (!preg_match("/^[a-zA-Z0-9ａ-ｚA-Z０-９]+$/", $str)){ 
            return null;// 英数以外の文字が含まれるなら，無効
        }
        $str = mb_convert_kana($str, "a");//全角英数を半角英数へ変換
        $str = strtoupper($str);//小文字を大文字に変換
   
        $pattern = implode('|',array_keys(KsuCode::FACULTY_DEPT));//学部IDのパターン
        if (preg_match('/^\d{2}('. $pattern . ')\d{3}$/', $str)){
            return $str;
        }
        $pattern = implode('|',array_keys(KsuCode::GRADUATE_SCHL));//大学院IDのパターン
        if (preg_match('/^\d{2}('. $pattern . ')\d{2}$/', $str)){
            return $str;
        }
        return null;
    }
}
<?php
namespace aic\views;
class Html
{
    public static function toList($data, $names=[])
    {
        $html = '<table class="table table-hover">'. PHP_EOL;
        foreach ($data as $key=>$value){
            $name = isset($names[$key]) ? $names[$key] : $key;
            $html .= sprintf('<tr><th>%s</th><td>%s</td></tr>', $name, $value). PHP_EOL;
        }
        return $html . '</table>'. PHP_EOL;

    }
    public static function input($tag, $name, $value=null, $attrs=null)
    {
        if (in_array($tag, ['text','number','date', 'datetime', 'datetime-local', 'hidden','range'])){
            return sprintf('<input type="%s" class="form-control" name="%s" value="%s" %s>', $tag, $name, $value, $attrs);            
        }
        return null;
    } 

    public static function textarea($name, $value=null, $attrs=null)
    {
        return sprintf('<textarea name="%s" %s>%s</textarea>', $name, $attrs, $value);
    }
    
    public static function select($options, $name, $selected=[], $tag='select'){
        $tag = strtolower($tag);
        $html = $s_tag = $c_tag = '';
        if (in_array($tag, ['select', 'radio', 'checkbox'])){
            if ($tag=='select'){
                $s_tag = '<select name="' . $name .'" class="form-control">'. PHP_EOL;
                $c_tag = '</select>';
                $o_tag = '<option value="%s" %s>%s</option>';
            }else{
                $o_tag = '<div class="form-check form-check-inline">';
                $o_tag .= '<input type="' . $tag . '" name="'.$name.'" value="%s" class="form-check-input" %s>';
                $o_tag .= '<label class="form-check-label">%s</label></div>';
            }
            $html .= $s_tag;
            foreach ($options as $key=>$value){
                $choice = in_array($key, $selected) ? ($tag=='select' ? 'selected' : 'checked') : '';
                $html .= sprintf($o_tag, $key, $choice, $value) . PHP_EOL;
            }
            return $html . $c_tag;
        }
        return null;
    }

    public static function pagination($total_rows, $page_rows, $page)
    {
        $url = $_SERVER['PHP_SELF'];
        $qstr = $_SERVER['QUERY_STRING'];
        $total_pages = ceil($total_rows / $page_rows);
        $first_page = floor(($page-1)/10)*10 + 1;
        $last_page = min($first_page + 9, $total_pages);
        $prev = $first_page - 10;
        $next = $first_page + 10;
        parse_str($qstr, $query);
    
        $html = '<ul class="pagination">' . PHP_EOL;
        $item = '<li class="page-item %s"><a class="page-link" href="%s">%s</a></li>' . PHP_EOL;
        $_label = 'Previous';
        $query['page'] = $prev;
        $_url = $url . '?' .http_build_query($query); 
        $_disabled = ($first_page < 10) ? 'disabled' : '';
        $html .= sprintf($item, $_disabled, $_url, $_label);
        
        for($p = $first_page; $p<=$last_page; $p++){
            $query['page'] = $p;
            $_url = $url . '?' .http_build_query($query); 
            $_disabled = ($p==$page) ? 'disabled' : '';
            $html .= sprintf ($item,  $_disabled, $_url, $p);
        }
        $_label = 'Next';
        $query['page'] = $next;
        $_url = $url . '?' .http_build_query($query); 
        $_disabled = ($next > $total_pages ) ? 'disabled' : '';
        $html .= sprintf($item, $_disabled, $_url, $_label);
        return $html . '</ul>' . PHP_EOL;
    }

}
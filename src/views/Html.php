<?php
class Html
{
    public static function select($options, $name, $selected=[],$tag='select'){
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

    public static function input($tag, $name, $value=null, $attrs=null)
    {
        if (in_array($tag, ['text','number','date', 'hidden','range'])){
            return sprintf('<input type="%s" class="form-control" name="%s" value="%s" %s>', $tag, $name, $value, $attrs);            
        }
        return null;
    } 

    public static function textarea($name, $value=null, $attrs=null)
    {
        return sprintf('<textarea name="%s" %s>%s</textarea>', $name, $attrs, $value);
    }
}
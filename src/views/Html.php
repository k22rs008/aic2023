<?php
class Html
{
    public static function select($options, $tag='select', $name, $selected=[]){
        $tag = strtolower($tag);
        $html = $s_tag = $c_tag = '';
        if (in_array($tag, ['select', 'radio', 'checkbox'])){
            if ($tag=='select'){
                $s_tag = '<select name="' . $name .'">'. PHP_EOL;
                $c_tag = '</select>'. PHP_EOL;
                $o_tag = '<option value="%s" %s>%s</option>'. PHP_EOL;
            }else{
                $o_tag = '<input type="' . $tag . '" name="'.$name.'" value="%s" class="form-check-input" %s>%s' . PHP_EOL;
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
}
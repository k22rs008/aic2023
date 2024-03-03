<?php
namespace aic\models;

class Util {
    public static function jpdate($date, $withtime=false, $withyear=true){
        $wdays = ['日','月','火','水','木','金','土'];
        $_date = new \DateTimeImmutable($date);
        $w = $_date->format('w');
        $y = $_date->format('Y');
        $time = $withtime ? $_date->format('H:i') : '';
        if ($withyear){
            $nengo = $y > 2019 ? '令和'.$y-2018 : '平成'. $y-1998;
            return $nengo . $_date->format('年n月d日('). $wdays[$w]. ')' . $time;
        }
        return $_date->format('n月d日(') . $wdays[$w]. ')' . $time;
    }

        /** transform a row list to a KVP list: Key-Value Pair */
        public static function toKVP($data, $key_field, $value_field)
        {
            $options = [];
            foreach($data as $row){
                $key = $row[$key_field];
                $value = $row[$value_field];
                $options[$key] = $value;
            }
            return $options;
        }
        
    public static function unifom_rand($items, $n=1)
    {
        $indexes = array_rand($items, $n);
        return self::array_slice_by_index($items, $indexes);
    }

    public static function array_slice_by_index($items, $indexes)
    {
        $sliced = [];
        if (is_scalar($indexes)) {
            $indexes = [$indexes];
        }
        foreach($indexes as $i){
            $sliced[] = $items[$i];
        }
        return $sliced;
    }

    /** prob_rand() : returns n elements from an array selected randpmly at unform
     * Example: prob_rand(['J'=>10, 'Q'=>25, 'K'=>15, 'A'=>50], 2) => ['Q','K']
     */
    public static function prob_rand($prob_items, $n=1) 
    {        
        $keys = array_keys($prob_items);
        $n_item = count($keys); 
        if ($n < 1 or $n > $n_item){
            return null;
        }
        // Transfer value => probility
        $_values = array_values($prob_items);  
        $_total = array_sum($_values);    
        $_prob = array_map(fn($v):float=>$v/$_total, $_values);
        $new_items = array_combine($keys, $_prob);  // print_r($new_items);
        
        // Generate combined keys of size $n, e.g.,
        // ['J','Q', 'K', 'A'] => [['J','Q'],['Q','K'],['K','A']]
        $comb_keys = self::combination($keys, $n);    // print_r($comb_keys);
        $comb_values = [];
        foreach ($comb_keys as $i=>$key){
            $comb_prob = array_map(fn($a):float=>$new_items[$a], $key);
            $comb_values[$i] = array_product( $comb_prob);
        }  // print_r($comb_values);
        
        // Randomly choose one combined key with combined probability 
        $total = array_sum($comb_values);
        $stop_at = rand(0, 100); 
        $curr_prob = 0;         
        foreach ($comb_keys as $i=>$item) {
            $curr_prob += 100 * $comb_values[$i] / $total; 
            if ($curr_prob >= $stop_at) {
                return $item;
            }
        }  
        return null;
    }

    static function combination($items, $n)
    {
        $items = array_values(array_unique($items));
        $n_item = count($items); 
        if ($n < 1 or $n > $n_item){
            return null;
        }
        $m = $n_item - $n + 1;
        $matrix = [];
        foreach (range(0, $n-1) as $i){
            foreach(array_slice($items, $i, $m) as $j=>$item){
                $matrix[$i][$j] = $item;
            }
        }
        // print_r($matrix);
        
        $result = [];
        foreach (range(0, $m-1) as $i){
            $result[] = array_column($matrix, $i);
        }        
        // print_r($result);
        return $result;

    }
}
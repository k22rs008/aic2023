<?php
function jpdate($date, $withtime=false){
    $wdays = ['日','月','火','水','木','金','土'];
    $_date = new \DateTimeImmutable($date);
    $w = $_date->format('w');
    $y = $_date->format('Y');
    $time = $withtime ? $_date->format('H:i') : '';
    $nengo = $y > 2019 ? '令和'.$y-2018 : '平成'. $y-1998;
    return $nengo . $_date->format('年n月d日('). $wdays[$w]. ')' . $time;
}

function array_randn($all, $n=1)
{
    $indexes = array_rand($all, $n);
    return array_slice_by_index($all, $indexes);
}

function array_slice_by_index($all, $indexes)
{
    $sliced = [];
    if (is_scalar($indexes)) {
        $indexes = [$indexes];
    }
    foreach($indexes as $i){
        $sliced[] = $all[$i];
    }
    return $sliced;
}

function rand_prob($items) {
    $total = array_sum(array_values($items));
    $stop_at = rand(0, 100); 
    $curr_prob = 0; 
  
    foreach ($items as $item => $prob) {
        $curr_prob += 100 * $prob / $total; 
        if ($curr_prob >= $stop_at) {
            return $item;
        }
    }  
    return null;
}
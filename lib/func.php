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

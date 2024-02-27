<?php

function toSQL($table, $data)
{
    if (!$data) return null;
    $row = $data[0];
    $fields = implode(',',array_keys($row));    
    $sql = sprintf('INSERT INTO %s (%s) VALUES' . PHP_EOL, $table, $fields);
    $rows = [];
    foreach ($data as $row){
        foreach ($row as $k => $v){
            if (is_string($v)){
                $row[$k] = "'" . $v. "'";
            }
        }
        $rows[] = sprintf('(%s)', implode(',', array_values($row)));
    }
    return $sql . implode(',' . PHP_EOL, $rows);
}

/**
 * Choose n elements from $all
 * e.g. sample([1,2,3,4,5], 3) => possible outputs: [1,2,4], [1,3,5]
 */ 
function sample($all, $n=1)
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


function disp_table($data)
{
    if (count($data)==0) return;
    $row = $data[0];
    echo '<table>'. PHP_EOL;
    echo "<tr>";
    foreach(array_keys($row) as $key){
        echo sprintf('<td>%s</td>',$key);
    } 
    echo " </tr>", PHP_EOL;
    foreach ($data as $row){
        echo "<tr>";
        foreach(array_values($row) as $val){
            echo sprintf('<td>%s</td>',$val);
        }
        echo " </tr>", PHP_EOL;
    }
    echo '</table>'. PHP_EOL;
}
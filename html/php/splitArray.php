<?php
// splits array into smaller arrays of equal length
function splitArray($array, $length) {
    $rows = array();
    $temp_array = array();

    for($i = 0; $i < count($array); $i++) {
        // reset sub-array if sub-array size is reached
        if($i % $length == 0) {
            $temp_array = array();
        }
        // add sub-array as row before reset
        else if($i % $length == $length - 1) {
            $temp_array[] = $array[$i];
            $rows[] = $temp_array;
        }

        $temp_array[] = $array[$i];
    }

    return $rows;
}
?>
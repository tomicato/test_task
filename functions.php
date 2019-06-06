<?php

function debug($arr){
   echo '<pre>'. print_r($arr, true) .'</pre>';
}

function _search(array $array, int $item, int $start = 0, int $end = null): int
{
    if($end === null){
        $end = count($array)-1;
    }

    if($start > $end){
        throw new LogicException("Not found Item!");
    }

    $half = (int)(($start + $end) / 2);

    if($array[$half] !== $item){
        if($array[$half] < $item){
            $start = $half + 1;
        }else{
            $end = $half - 1;
        }

        return _search($array, $item, $start, $end);
    }

    return  $half;

}
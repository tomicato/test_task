<?php

$array = [1, 2, 5, 7, 8, 10, 15, 100];
$item = 15;

function binarySearch2(array $array, int $item, int $start = 0, int $end = null): int
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

        return binarySearch2($array, $item, $start, $end);
    }

   return  $half;

}

echo binarySearch2($array, $item);

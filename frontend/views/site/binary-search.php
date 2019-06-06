<?php


$array = [1, 2, 5, 7, 8, 10, 15, 100];
$item = 100;

function binarySearch(array $array, int $item, int $start = 0,  int $end = null): int
{
    if($end === null){
        $end = count($array) -1;
    }

    if($start > $end){
        throw new LogicException("Not found Item");
    }
    $halfIndex = (int)(($start + $end) / 2);

    if($array[$halfIndex] !== $item){
        if($array[$halfIndex] < $item){
            $start = $halfIndex + 1;
        }else{
            $end = $halfIndex -1;
        }
        return binarySearch($array, $item, $start, $end);
    }
    return $halfIndex;
}

echo binarySearch($array, $item);

$a = 2.5;
echo (int)$a;
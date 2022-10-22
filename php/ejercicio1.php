<?php

// EJERCICIO 1

function quicksort(&$array, $left, $right, $column) 
{
    if ($left >= $right) {
        return;
    }

    $pivot = $array[($left + $right) / 2][$column];
    $index = partition($array, $left, $right, $pivot, $column);
    quicksort($array, $left, $index - 1, $column);
    quicksort($array, $index, $right, $column);
}

function partition(&$array, $left, $right, $pivot, $column)
{
    while ($left <= $right) {
        while ($array[$left][$column] < $pivot) {
            $left++;
        }
        while ($array[$right][$column] > $pivot) {
            $right--;
        }
        if ($left <= $right) {
            swap($array, $left, $right);
            $left++;
            $right--;
        }
    }

    return $left;
}

function swap(&$array, $left, $right)
{
    $aux = $array[$left];
    $array[$left] = $array[$right];
    $array[$right] = $aux;
}

function sort_csv($csv, $column)
{
    $csvData = explode("\n", $csv);
    $csvData = array_map(function ($row) {
        return explode(',', $row);
    }, $csvData);

    quicksort($csvData, 0, count($csvData) - 1, $column);

    $csvData = array_map(function ($row) {
        return implode(',', $row);
    }, $csvData);

    return implode("\n", $csvData);
}

$data = <<<CSV
paco,2,3.6
perico,5,1.7
pirolo,1.3,2.6
pocholo,11,-1.5
CSV;

var_dump(sort_csv($data, 1));
var_dump(sort_csv($data, 2));

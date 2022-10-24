<?php

// EXERCISE 1

function clear_duplicates(array $array): array
{
    $output = [];
    $index = [];
    for ($i = count($array) - 1; $i >= 0; $i--) {
        if (!isset($index[$array[$i]])) {
            $index[$array[$i]] = true;
            array_unshift($output, $array[$i]);
        }
    }

    return $output;
}

echo "EXERCISE 1\n";
print_r(clear_duplicates([1, 2, 10, 3, 4, 9, 5, 6, 8, 7, 8, 9, 10]));
echo "\n";


// EXERCISE 2

function is_periodic(string $periodicString): bool
{
    $length = strlen($periodicString);
    $factor = 1;
    do {
        $subtr = substr($periodicString, 0, $factor);
        $repeated = str_repeat($subtr, (int) $length / $factor);
        if ($repeated == $periodicString) {
            return true;
        }
        $factor++;
    } while ($factor < $length);

    return false;
}


function next_periodic(int $n): int
{
    do {
        $binary = int_to_binary(++$n);
    } while(!is_periodic($binary));

    return $n;
}

function int_to_binary(int $n): string
{
    $binary = [];
    $i = 0;
    while ($n > 0)
    {
        $binary[$i] = $n % 2;
        $n = (int)($n / 2);
        $i++;
    }

    $binary = array_reverse($binary);

    return implode('', $binary);
}

echo "EXERCISE 2\n";
print_r(is_periodic("blablablabla") ? "true\n" : "false\n");
print_r(is_periodic("blablebli") ? "true\n" : "false\n");
print_r(is_periodic("blablabl") ? "true\n" : "false\n");
print_r(is_periodic("101101101") ? "true\n" : "false\n");
print_r(is_periodic("1010101010") ? "true\n" : "false\n");

print_r(int_to_binary(365)); echo "\n";
print_r(int_to_binary(682)); echo "\n";
print_r(next_periodic(300)); echo "\n";
echo "\n";


// EXERCISE 3

function rectangles_intersect_area(...$coordinates)
{
    [$_x1, $_y1, $_x2, $_y2, $_x3, $_y3, $_x4, $_y4] = $coordinates;

    [$x1, $y1] = getLeftBottom($_x1, $_y1, $_x2, $_y2);
    [$x2, $y2] = getRightTop($_x1, $_y1, $_x2, $_y2);
    [$x3, $y3] = getLeftBottom($_x3, $_y3, $_x4, $_y4);
    [$x4, $y4] = getRightTop($_x3, $_y3, $_x4, $_y4);

    $xdist = min([$x2, $x4]) - max([$x1, $x3]);
    $ydist = min([$y2, $y4]) - max([$y1, $y3]);

    if ($xdist > 0 && $ydist > 0) {
        return $xdist * $ydist;
    }

    return 0;
}

function getLeftBottom(...$coordinates)
{
    [$x1, $y1, $x2, $y2] = $coordinates;

    return [min([$x1, $x2]), min([$y1, $y2])];
}

function getRightTop(...$coordinates)
{
    [$x1, $y1, $x2, $y2] = $coordinates;

    return [max([$x1, $x2]), max([$y1, $y2])];
}

echo "EXERCISE 3\n";
$ex1 = [0,0,20,20,10,10,30,30];
print_r(rectangles_intersect_area(...$ex1)); echo "\n";
$ex2 = [0,20,20,0,10,30,30,10];
print_r(rectangles_intersect_area(...$ex2)); echo "\n";
$ex3 = [0,0,30,30,10,10,20,20];
print_r(rectangles_intersect_area(...$ex3)); echo "\n";
$ex4 = [0,20,30,0,10,10,30,20];
print_r(rectangles_intersect_area(...$ex4)); echo "\n";
$ex5 = [0,0,10,10,20,20,30,30];
print_r(rectangles_intersect_area(...$ex5)); echo "\n";
echo "\n";

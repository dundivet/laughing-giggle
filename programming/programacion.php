<?php

// EJERCICIO 1

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

print_r(clear_duplicates([1, 2, 10, 3, 4, 9, 5, 6, 8, 7, 8, 9, 10]));
echo "\n";



// EJERCICIO 2

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

print_r(is_periodic("blablablabla") ? "true\n" : "false\n");
print_r(is_periodic("blablebli") ? "true\n" : "false\n");
print_r(is_periodic("blablabl") ? "true\n" : "false\n");
print_r(is_periodic("101101101") ? "true\n" : "false\n");
print_r(is_periodic("1010101010") ? "true\n" : "false\n");

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

print_r(int_to_binary(365)); echo "\n";
print_r(int_to_binary(682)); echo "\n";

print_r(next_periodic(300)); echo "\n";



// EJERCICIO 3



<?php

declare(strict_types=1);

$numArray = [1,1,1,2,2,2,3,3];


$series = function () use ($numArray): \Generator {
    yield from $numArray;
};


$gen = $series();

$set = function () use ($gen): \Generator {
    $arr = [];
    $i = 0;
    while ($gen->valid()) {
        $current = function () use ($gen) {
            return $gen->current();
        };

        $next = function () use ($gen) {
            $gen->next();
            return $gen->current();
        };

        $arr[$i++] = $current();

        if ($current() != $next()) {
            yield $arr;
            $i=0;
            $arr = [];
        }
    }
};

foreach ($set() as $val) {
    var_dump($val);
}

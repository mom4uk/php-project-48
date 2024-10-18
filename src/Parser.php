<?php

namespace Parser;

use function General\isAssociativeArray;
use function Formatters\Stylish\stylish;
use function Formatters\Plain\plain;

function format($data, $format)
{
    switch ($format) {
        case 'stylish':
            return stylish($data);;
        case 'plain':
            // dump(plain($data));
            return plain($data);
    }
}

function constructDiff($coll1, $coll2, $key, $value)
{
    if (array_key_exists($key, $coll1) && array_key_exists($key, $coll2) && isAssociativeArray($coll1[$key]) && isAssociativeArray($coll2[$key])) {
        return ['key' => $key, 'children' => $value, 'flag' => ' '];
    }
    if (array_key_exists($key, $coll1) && array_key_exists($key, $coll2) && $coll1[$key] === $coll2[$key]) {
        return ['key' => $key, 'value' => $value, 'flag' => ' ', 'children' => []];
    }
    if (array_key_exists($key, $coll1) && !array_key_exists($key, $coll2)) {
        return ['key' => $key, 'value' => $value, 'flag' => '-', 'children' => []];
    }
    if (!array_key_exists($key, $coll1) && array_key_exists($key, $coll2)) {
        return ['key' => $key, 'value' => $coll2[$key], 'flag' => '+', 'children' => []];
    }
    return [
                ['key' => $key, 'value' => $coll1[$key], 'flag' => '-', 'children' => []],
                ['key' => $key, 'value' => $coll2[$key], 'flag' => '+', 'children' => []]
            ];
}

function getDiff($coll1, $coll2)
{
    $unique_keys = array_unique(array_merge(array_keys($coll1), array_keys($coll2)));
    sort($unique_keys);
    // mb you need to use array_values and work with it? to reduce if constructions
    $diff = array_map(function ($key) use ($coll1, $coll2) {
        $isntArraysAssociativeArr = array_key_exists($key, $coll1) && !isAssociativeArray($coll1[$key]) || array_key_exists($key, $coll2) && !isAssociativeArray($coll2[$key]);
        $isOneOfValuesExist = array_key_exists($key, $coll1) && !array_key_exists($key, $coll2) || array_key_exists($key, $coll2) && !array_key_exists($key, $coll1);
        if ($isntArraysAssociativeArr || $isOneOfValuesExist) {
            if (!array_key_exists($key, $coll1)) {
                return constructDiff($coll1, $coll2, $key, $coll2[$key]);
            }
            return constructDiff($coll1, $coll2, $key, $coll1[$key]);
        }
        return constructDiff($coll1, $coll2, $key, getDiff($coll1[$key], $coll2[$key]));
    },
    $unique_keys);
    return $diff;
}

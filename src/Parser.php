<?php

namespace Parser;

use function General\isAssociativeArray;
use function Formatters\Stylish\makeStylish;
use function Formatters\Plain\makePlain;
use function Formatters\Json\makeJson;
use function Functional\sort;

function format(array $data, string $format)
{
    switch ($format) {
        case 'stylish':
            return makeStylish($data);
        case 'plain':
            return makePlain($data);
        case 'json':
            return makeJson($data);
    }
}

function constructDiff(array $coll1, array $coll2, string $key, string|array|null|bool $value)
{
    if (
        array_key_exists($key, $coll1) && array_key_exists($key, $coll2)
        && isAssociativeArray($coll1[$key]) && isAssociativeArray($coll2[$key])
    ) {
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

function getDiff(array $coll1, array $coll2)
{
    $uniqueKeys = array_unique(array_merge(array_keys($coll1), array_keys($coll2)));
    $sortedKeys = sort($uniqueKeys, fn($first, $second) => $first <=> $second);
    // mb you need to use array_values and work with it? to reduce if constructions
    $diff = array_map(function ($key) use ($coll1, $coll2) {
        $isntArraysAssociativeArr = array_key_exists($key, $coll1) && !isAssociativeArray($coll1[$key])
        || array_key_exists($key, $coll2) && !isAssociativeArray($coll2[$key]);
        $isOneOfValuesExist = array_key_exists($key, $coll1) && !array_key_exists($key, $coll2)
        || array_key_exists($key, $coll2) && !array_key_exists($key, $coll1);
        if ($isntArraysAssociativeArr || $isOneOfValuesExist) {
            if (!array_key_exists($key, $coll1)) {
                return constructDiff($coll1, $coll2, $key, $coll2[$key]);
            }
            return constructDiff($coll1, $coll2, $key, $coll1[$key]);
        }
        return constructDiff($coll1, $coll2, $key, getDiff($coll1[$key], $coll2[$key]));
    },
    $sortedKeys);
    return $diff;
}

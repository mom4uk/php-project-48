<?php

namespace Parser;

use Symfony\Component\Yaml\Yaml;

use function General\isAssociativeArray;
use function General\getFormat;

function format($data, $format)
{
    switch ($format) {
        case 'stylish':
            $x = stylish($data); 
            dump($x);
            return $x;
    }
}


function stylish($value)
{
    $iter = function ($spacesCount, $depth, $currentValue) use (&$iter) {
        $intent = ' ';
        $intentSize = $depth * $spacesCount;
        $frontIntent = str_repeat($intent, $intentSize);
        $backIntent = str_repeat($intent, $intentSize - $spacesCount);
        $normalizedValue = fn ($item) => gettype($item) === 'boolean' || gettype($item) === 'NULL' ? json_encode($item) : $item;

        $lines = array_map(function ($item) use (&$iter, $depth, $spacesCount,$frontIntent) {
            
            $normalizedValue = fn ($item) => gettype($item) === 'boolean' || gettype($item) === 'NULL' ? json_encode($item) : $item;
            if (!array_key_exists('children', $item)) {
                [['key' => $key1, 'value' => $value1, 'flag' => $flag1], ['key' => $key2, 'value' => $value2, 'flag' => $flag2]] = $item;
                return "{$frontIntent}{$flag1} {$key1}: {$normalizedValue($value1)} \n{$frontIntent}{$flag2} {$key2}: {$normalizedValue($value2)}";
            }
            if (array_key_exists('children', $item) && count($item['children']) === 0) {
                ['key' => $key, 'value' => $value, 'flag' => $flag] = $item;
                return "{$frontIntent}{$flag} {$key}: {$normalizedValue($value)}";
            }
            ['key' => $key, 'children' => $children, 'flag' => $flag] = $item;
            return "{$frontIntent}{$flag} {$key}: {$iter($spacesCount, $depth + 1, $children)}";
        }, $currentValue);
        $compose = implode("\n", ['{', ...$lines, "{$backIntent}}"]);
        return $compose;
    };
    return $iter(2, 1, $value);
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
    $diff = array_map(function ($key) use ($coll1, $coll2) {
        if (!isAssociativeArray($coll1[$key]) || !isAssociativeArray($coll2[$key])) {
            return constructDiff($coll1, $coll2, $key, $coll1[$key]);
        }
        return constructDiff($coll1, $coll2, $key, getDiff($coll1[$key], $coll2[$key]));
    },
    $unique_keys);
    return $diff;
}

function getContents($filepath1, $filepath2)
{
    $format = getFormat($filepath1, $filepath2);
    $normalizedYamlFormat = $format === 'yml' ? 'yaml' : $format;
    switch ($normalizedYamlFormat) {
        case 'json':
            $file1Content = json_decode(file_get_contents($filepath1), true);
            $file2Content = json_decode(file_get_contents($filepath2), true);
            return [$file1Content, $file2Content];
        case 'yaml':
            $file1Content = Yaml::parse(file_get_contents($filepath1), Yaml::PARSE_OBJECT_FOR_MAP);
            $file2Content = Yaml::parse(file_get_contents($filepath2), Yaml::PARSE_OBJECT_FOR_MAP);
            $x = json_decode(json_encode($file1Content), true);
            $y = json_decode(json_encode($file2Content), true);
            return [$x, $y];
    }
}

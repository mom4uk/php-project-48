<?php

namespace Gendiff;

function parse($coll1, $coll2, $item)
{
    if (!array_key_exists($item, $coll1) && array_key_exists($item, $coll2)) {
        $normalized = gettype($coll2[$item]) === 'boolean' ? json_encode($coll2[$item]) : $coll2[$item];
        return "+ {$item}: {$normalized}";
    } elseif (array_key_exists($item, $coll1) && !array_key_exists($item, $coll2)) {
        $normalized = gettype($coll1[$item]) === 'boolean' ? json_encode($coll1[$item]) : $coll1[$item];
        return "- {$item}: {$normalized}";
    } elseif (array_key_exists($item, $coll1) && array_key_exists($item, $coll2) && $coll1[$item] !== $coll2[$item]) {
        return "- {$item}: {$coll1[$item]}\n+ {$item}: {$coll2[$item]}";
    } else {
        return "{$item}: {$coll1[$item]}";
    }
}

function genDiff($filepath1, $filepath2)
{
    $file1Content = json_decode(file_get_contents($filepath1), true);
    $file2Content = json_decode(file_get_contents($filepath2), true);
    $unique_keys = array_unique(array_merge(array_keys($file1Content), array_keys($file2Content)));
    sort($unique_keys);
    $addedItems = array_map(function ($item) use ($file1Content, $file2Content) {
        return parse($file1Content, $file2Content, $item);
    }, $unique_keys);
    return implode(PHP_EOL, $addedItems);
}

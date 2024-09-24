<?php

namespace Gendiff;

use function Parser\parse;
use function Parser\getContents;

function genDiff($filepath1, $filepath2)
{
    [$file1Content, $file2Content] = getContents($filepath1, $filepath2);
    $unique_keys = array_unique(array_merge(array_keys($file1Content), array_keys($file2Content)));
    sort($unique_keys);
    $addedItems = array_map(function ($item) use ($file1Content, $file2Content) {
        return parse($file1Content, $file2Content, $item);
    }, $unique_keys);
    $concatedItems = implode($addedItems);
    return "{\n{$concatedItems}}";
}

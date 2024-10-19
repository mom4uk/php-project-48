<?php

namespace Formatters\Stylish;

use function General\isAssociativeArray;
use function General\stringify;
use function General\normalizeValue;

function makeStylish($value)
{
    $iter = function ($spacesCount, $depth, $currentValue) use (&$iter) {
        $intent = ' ';
        $intentSize = $depth * $spacesCount;
        $frontIntent = str_repeat($intent, $intentSize);
        $backIntent = str_repeat($intent, $intentSize - $spacesCount);

        $lines = array_map(function ($item) use (&$iter, $depth, $spacesCount, $frontIntent) {

            $normalizedValue = fn ($item, $depth) =>
            (isAssociativeArray($item) ? stringify($item, $depth) : normalizeValue($item, $depth));

            if (!array_key_exists('children', $item)) {
                [
                    ['key' => $key1, 'value' => $value1, 'flag' => $flag1],
                     ['key' => $key2, 'value' => $value2, 'flag' => $flag2]
                ] = $item;
                return "{$frontIntent}{$flag1} {$key1}: {$normalizedValue($value1, $depth + 3)}" .
                 "\n" . "{$frontIntent}{$flag2} {$key2}: {$normalizedValue($value2, $depth + 3)}";
            }
            if (array_key_exists('children', $item) && count($item['children']) === 0) {
                ['key' => $key, 'value' => $value, 'flag' => $flag] = $item;
                return "{$frontIntent}{$flag} {$key}: {$normalizedValue($value, $depth + 3)}";
            }
            ['key' => $key, 'children' => $children, 'flag' => $flag] = $item;
            return "{$frontIntent}{$flag} {$key}: {$iter($spacesCount, $depth + 2, $children)}";
        }, $currentValue);

        $compose = implode("\n", ['{', ...$lines, "{$backIntent}}"]);
        return $compose;
    };
    return $iter(2, 1, $value);
}

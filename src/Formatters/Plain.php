<?php

namespace Formatters\Plain;

use function General\isAssociativeArray;
use function General\normalizeValue;

function constructLine($path, $value) {

    $normalizeValue = function ($value) {
        if (isAssociativeArray($value)) {
            return '[complex value]';
        }
        if (gettype($value) === 'boolean' || gettype($value) === 'NULL') {
            return normalizeValue($value);
        }
        $x = fn($item) => normalizeValue($item);
        return "'{$x($value)}'";
    };

    if (!isAssociativeArray($value)) {
        [['value' => $value1], ['value' => $value2]] = $value;
        return "Property '{$path}' was updated. From {$normalizeValue($value1)} to {$normalizeValue($value2)}";
    }
    ['value' => $value, 'flag' => $flag] = $value;
    if ($flag === '+') {
        return "Property '{$path}' was added with value: {$normalizeValue($value)}";
    }
    if ($flag === '-') {
        return "Property '{$path}' was removed";
    }
}

function plain($value)
{
    // dump($value);
    $iter = function ($currentValue, $path) use (&$iter) {
        $lines = array_filter(array_map(function ($item) use (&$iter, $path) {

            if (!array_key_exists('children', $item)) {
                [['key' => $key]] = $item;
                $newPath = $path === '' ? "{$key}" : "{$path}.{$key}";
                return constructLine($newPath, $item);
            }

            ['key' => $key] = $item;
            $newPath = $path === '' ? "{$key}" : "{$path}.{$key}";

            if (array_key_exists('children', $item) && $item['children'] === []) {
                return constructLine($newPath, $item);
            }
            return $iter($item['children'], $newPath);
        }, $currentValue), fn($item) => !is_null($item));
        // dump($lines);
        $combinedLines = implode("\n", $lines);
        return $combinedLines;
    };
    return $iter($value, '');
}
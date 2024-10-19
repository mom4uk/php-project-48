<?php

namespace Formatters\Plain;

use function General\isAssociativeArray;
use function General\normalizeValue;

function constructLine(string $path, array|string|int $value)
{

    $normalizeValue = function ($value) {
        if (isAssociativeArray($value)) {
            return '[complex value]';
        }
        if (gettype($value) === 'string') {
            $x = fn($item) => normalizeValue($item);
            return "'{$x($value)}'";
        }
        return normalizeValue($value);
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

function makePlain(array $value)
{
    $iter = function ($currentValue, $path) use (&$iter) {
        $lines = array_filter(array_map(function ($item) use (&$iter, $path) {

            if (!array_key_exists('children', $item)) {
                [['key' => $key]] = $item;
                $newPath = $path === '' ? "{$key}" : "{$path}.{$key}";
                return constructLine($newPath, $item);
            }

            ['key' => $key] = $item;
            $newPath = $path === '' ? "{$key}" : "{$path}.{$key}";

            if ($item['children'] === []) {
                return constructLine($newPath, $item);
            }
            return $iter($item['children'], $newPath);
        }, $currentValue), fn($item) => !is_null($item));
        $combinedLines = implode("\n", $lines);
        return $combinedLines;
    };
    return $iter($value, '');
}

<?php

namespace General;

function getFormat($filepath1, $filepath2)
{
    $splitedPath1 = explode('.', $filepath1);
    $splitedPath2 = explode('.', $filepath2);

    $firstFileFormat = $splitedPath1[count($splitedPath1) - 1];
    $secondFileFormat = $splitedPath2[count($splitedPath2) - 1];

    if ($firstFileFormat === $secondFileFormat) {
        return $firstFileFormat;
    }
    return "Error: missmatch in formats: first file format {$firstFileFormat}, second file format {$secondFileFormat}";
}

function isAssociativeArray($value)
{
    if (!is_array($value)) {
        return false;
    }
    $decodedValue = json_decode(json_encode($value), false);
    return !is_array($decodedValue);
}

function toString($value)
{
    return trim(var_export($value, true), "'");
}

function stringify($data, $depth, $spacesCount = 2, $replacer = ' ')
{

    $iter = function ($value, $depth) use (&$iter, $replacer, $spacesCount) {
        if (!is_array($value)) {
            return toString($value);
        }
        $intentSize = $depth * $spacesCount;
        $frontIntent = str_repeat($replacer, $intentSize);
        $backIntent = str_repeat($replacer, $intentSize - $spacesCount * 2);
        $lines = array_map(
            fn($key, $val) => "{$frontIntent}{$key}: {$iter($val, $depth + 2)}",
            array_keys($value),
            $value
        );

        $compose = ['{', ...$lines, "{$backIntent}}"];
        return implode("\n", $compose);
    };

    return $iter($data, $depth);
}

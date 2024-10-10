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
    $decodedValue = json_decode(json_encode($value), FALSE);
    return !is_array($decodedValue);
}
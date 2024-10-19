<?php

namespace General;

use Symfony\Component\Yaml\Yaml;

function normalizeValue(string|bool|null|int $value)
{
    return gettype($value) === 'boolean' || gettype($value) === 'NULL' ? json_encode($value) : $value;
}

function getFormat(string $filepath1, string $filepath2)
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

function isAssociativeArray(string|array|null|int $value)
{
    if (!is_array($value)) {
        return false;
    }
    $decodedValue = json_decode(json_encode($value), false);
    return !is_array($decodedValue);
}

function toString(int|string|null $value)
{
    return trim(var_export($value, true), "'");
}

function stringify(array $data, int $depth, int $spacesCount = 2, string $replacer = ' ')
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

function getContents(string $filepath1, string $filepath2)
{
    $format = getFormat($filepath1, $filepath2);
    $normalizedYamlFormat = $format === 'yml' ? 'yaml' : $format;
    $wrap = fn($item) :string => file_get_contents($item);
    switch ($normalizedYamlFormat) {
        case 'json':
            $file1Content = json_decode(strval(file_get_contents($filepath1)), true);
            $file2Content = json_decode(strval(file_get_contents($filepath2)), true);
            return [$file1Content, $file2Content];
        case 'yaml':
            $file1Content = Yaml::parse(strval(file_get_contents($filepath1)), Yaml::PARSE_OBJECT_FOR_MAP);
            $file2Content = Yaml::parse(strval(file_get_contents($filepath2)), Yaml::PARSE_OBJECT_FOR_MAP);
            $decodedContent1 = json_decode(json_encode($file1Content), true);
            $decodedContent2 = json_decode(json_encode($file2Content), true);
            return [$decodedContent1, $decodedContent2];
    }
}

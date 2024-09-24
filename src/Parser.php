<?php

namespace Parser;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use function General\getFormat;

function constructEntry($entry, $flag = '')
{
    $changeIndent = '  ';
    $unchangeIndent = '    ';
     
    [$key, $value] = $entry;
    $normalizedValue = gettype($value) === 'boolean' ? json_encode($value) : $value;

    $result = $flag === '' ? "{$unchangeIndent}{$key}: {$normalizedValue}\n" : "{$changeIndent}{$flag} {$key}: {$normalizedValue}\n";
    return $result;
}

function parse($coll1, $coll2, $item)
{
    if (!array_key_exists($item, $coll1) && array_key_exists($item, $coll2)) {
        return constructEntry([$item, $coll2[$item]], '+');
    } elseif (array_key_exists($item, $coll1) && !array_key_exists($item, $coll2)) {
        return constructEntry([$item, $coll1[$item]], '-');
    } elseif (array_key_exists($item, $coll1) && array_key_exists($item, $coll2) && $coll1[$item] !== $coll2[$item]) {
        $firstValue = constructEntry([$item, $coll1[$item]], '-');
        $secondValue = constructEntry([$item, $coll2[$item]], '+');
        return "{$firstValue}{$secondValue}";
    } else {
        return constructEntry([$item, $coll1[$item]]);
    }
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

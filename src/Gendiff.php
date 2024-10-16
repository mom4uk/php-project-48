<?php

namespace Gendiff;

use function Parser\getDiff;
use function Parser\getContents;
use function Parser\format;

function genDiff($filepath1, $filepath2, $formatName = 'stylish')
{
    $contents = getContents($filepath1, $filepath2);
    $diff = getDiff(...$contents);
    $formatedDiff = format($diff, $formatName);
    return $formatedDiff;
}

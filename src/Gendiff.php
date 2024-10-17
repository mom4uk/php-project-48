<?php

namespace Gendiff;

use function Parser\getDiff;
use function General\getContents;
use function Parser\format;

function genDiff($filepath1, $filepath2, $format = 'stylish')
{
    $contents = getContents($filepath1, $filepath2);
    $diff = getDiff(...$contents);
    $formatedDiff = format($diff, $format);
    return $formatedDiff;
}

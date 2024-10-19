<?php

namespace Differ\Differ;

use function Parser\getDiff;
use function General\getContents;
use function Parser\format;

function genDiff(string $filepath1, string $filepath2, string $format = 'stylish')
{
    $contents = getContents($filepath1, $filepath2);
    $diff = getDiff(...$contents);
    $formatedDiff = format($diff, $format);
    return $formatedDiff;
}

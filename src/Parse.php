<?php

namespace Parse;

function parse($filepath1, $filepath2)
{
    if (!file_exists($filepath1)) {
        return 'file not exists';
    }
    $file1Content = file_get_contents($filepath1);
    $file2Content = file_get_contents($filepath2);
    return "{$file1Content} \n{$file2Content}";
}
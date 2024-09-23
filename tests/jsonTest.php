<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\genDiff;

class JsonTest extends TestCase
{
    /** @test */
    public function jsonTest(): void
    {
        $expected = '{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}';
          
        $firstFilePath = './tests/fixtures/file1.json';
        $secondFilePath = './tests/fixtures/file2.json';
        
        $this->assertEquals($expected, genDiff($firstFilePath, $secondFilePath));
    }
}
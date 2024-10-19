<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\genDiff;

class JsonTest extends TestCase
{
  private string $path = __DIR__ . "/fixtures/";
  private $expected;

  public function setUp(): void
  {
    $this->expected = '';
  }

  private function getFilePath($name)
  {
    return $this->path . $name;
  }

    /** @test */
    public function jsonTest(): void
    {
        $firstFilePath = $this->getFilePath('file1.json');
        $secondFilePath = $this->getFilePath('file2.json');
        $diff = genDiff($firstFilePath, $secondFilePath, 'json');
        $this->assertEquals($this->expected, $diff);
    }
    /** @test */
    public function yamlTest(): void
    {     
        $firstFilePath = $this->getFilePath('file1.yaml');
        $secondFilePath = $this->getFilePath('file2.yaml');
        $diff = genDiff($firstFilePath, $secondFilePath, 'json');
        $this->assertEquals($this->expected, $diff);
    }
}
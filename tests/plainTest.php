<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class PlainTest extends TestCase
{
  private string $path = __DIR__ . "/fixtures/";
  private $expected;

  public function setUp(): void
  {
    $this->expected = 'Property \'common.follow\' was added with value: false
Property \'common.setting2\' was removed
Property \'common.setting3\' was updated. From true to null
Property \'common.setting4\' was added with value: \'blah blah\'
Property \'common.setting5\' was added with value: [complex value]
Property \'common.setting6.doge.wow\' was updated. From \'\' to \'so much\'
Property \'common.setting6.ops\' was added with value: \'vops\'
Property \'group1.baz\' was updated. From \'bas\' to \'bars\'
Property \'group1.nest\' was updated. From [complex value] to \'str\'
Property \'group2\' was removed
Property \'group3\' was added with value: [complex value]';
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
        $diff = genDiff($firstFilePath, $secondFilePath, 'plain');
        $this->assertEquals($this->expected, $diff);
    }
    /** @test */
    public function yamlTest(): void
    {     
        $firstFilePath = $this->getFilePath('file1.yaml');
        $secondFilePath = $this->getFilePath('file2.yaml');
        $diff = genDiff($firstFilePath, $secondFilePath, 'plain');
        $this->assertEquals($this->expected, $diff);
    }
}
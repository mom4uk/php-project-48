<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\genDiff;

class ParseTest extends TestCase
{
  private string $path = __DIR__ . "/fixtures/";

  private function getFilePath($name)
  {
    return $this->path . $name;
  }

    /** @test */
    public function jsonTest(): void
    {
        $expected = '{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: 
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}';
        $firstFilePath = $this->getFilePath('file1.json');
        $secondFilePath = $this->getFilePath('file2.json');
        
        $this->assertEquals($expected, genDiff($firstFilePath, $secondFilePath));
    }
    /** @test */
    public function yamlTest(): void
    {
        $expected = '{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: 
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}';
          
        $firstFilePath = $this->getFilePath('file1.yaml');
        $secondFilePath = $this->getFilePath('file2.yaml');
        
        $this->assertEquals($expected, genDiff($firstFilePath, $secondFilePath));
    }
}
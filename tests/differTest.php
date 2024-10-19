<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private string $path = __DIR__ . "/fixtures/";

    private function getFilePath($name)
    {
        return $this->path . $name;
    }

    /** @test */
    public function DiffTestJson(): void
    {     
        $firstFilePathJson = $this->getFilePath('file1.json');
        $secondFilePathJson = $this->getFilePath('file2.json');

        $firstFilePathYaml = $this->getFilePath('file1.yaml');
        $secondFilePathYaml = $this->getFilePath('file2.yaml');

        $stylishExpectedPath = $this->getFilePath('../fixtures/rightStylish');
        $plainExpectedPath = $this->getFilePath('rightPlain');
        $jsonExpectedPath = $this->getFilePath('rightJson');

        $stylishExpected = file_get_contents($stylishExpectedPath);
        $this->assertEquals($stylishExpected, genDiff($firstFilePathJson, $secondFilePathJson, 'stylish'));
        $this->assertEquals($stylishExpected, genDiff($firstFilePathYaml, $secondFilePathYaml, 'stylish'));

        $plainExpected = file_get_contents($plainExpectedPath);
        $this->assertEquals($plainExpected, genDiff($firstFilePathJson, $secondFilePathJson, 'plain'));
        $this->assertEquals($plainExpected, genDiff($firstFilePathYaml, $secondFilePathYaml, 'plain'));

        $jsonExpected = file_get_contents($jsonExpectedPath);
        $this->assertEquals($jsonExpected, genDiff($firstFilePathJson, $secondFilePathJson, 'json'));
        $this->assertEquals($jsonExpected, genDiff($firstFilePathYaml, $secondFilePathYaml, 'json'));
    }
}

<?php

namespace BYanelli\CsvQuery\Tests;

use BYanelli\CsvQuery\CsvFile;
use PHPUnit\Framework\TestCase;

class CsvFileTest extends TestCase
{
    protected function resourcesPath(string $file='')
    {
        $separator = $file? DIRECTORY_SEPARATOR : '';

        return __DIR__.DIRECTORY_SEPARATOR.'resources'.$separator.$file;
    }

    public function testOpenCsv()
    {
        $file = new CsvFile($this->resourcesPath('test.csv'));

        $file->test();

        $this->assertTrue(true); //todo
    }
}

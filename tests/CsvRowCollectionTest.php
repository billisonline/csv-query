<?php

namespace BYanelli\CsvQuery\Tests;

use BYanelli\CsvQuery\CsvRowCollection;
use BYanelli\CsvQuery\Tests\Concerns\LocatesResources;
use PHPUnit\Framework\TestCase;

class CsvRowCollectionTest extends TestCase
{
    use LocatesResources;

    protected function getTestCsvCollection()
    {
        return new CsvRowCollection($this->getTestCsv());
    }

    public function testWhereEquals()
    {
        $row = (
            $this->getTestCsv()
                ->rows()
                ->where('name/last', 'Grant')
                ->first()
        );

        $this->assertEquals('Alfred', $row['name/first']);
    }

    public function testWhereGreaterThan()
    {
        $seniorsCount = (
            $this->getTestCsv()
                ->rows()
                ->where('age', '>=', 65)
                ->count()
        );

        $this->assertEquals(3, $seniorsCount);
    }

    public function testPluck()
    {
        $val = (
            $this->getTestCsv()
                ->rows()
                ->pluck('city')
                ->first()
        );

        $this->assertEquals('Ecgeude', $val);
    }

    public function testAverage()
    {
        $avg = (
            $this->getTestCsv()
                ->rows()
                ->average('age')
        );

        $this->assertEquals(40.38, $avg);
    }

    // todo: test more collection methods
}

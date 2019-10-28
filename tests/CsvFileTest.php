<?php

namespace BYanelli\CsvQuery\Tests;

use BYanelli\CsvQuery\Tests\Concerns\LocatesResources;
use PHPUnit\Framework\TestCase;

class CsvFileTest extends TestCase
{
    use LocatesResources;

    public function testIterateLines()
    {
        $i = 0;

        foreach ($this->getTestCsv()->lines() as $line) {
            $i++;
        }

        $this->assertEquals(100, $i);
    }

    public function testIterateRows()
    {
        $i = 0;

        foreach ($this->getTestCsv()->rows() as $row) {
            $i++;
        }

        $this->assertEquals(100, $i);
    }

    public function testReadCell()
    {
        foreach ($this->getTestCsv()->rows() as $row) {
            $this->assertEquals('Birdie', $row['name/first']);
            $this->assertEquals('Fox', $row['name/last']);

            $this->assertEquals('Birdie', $row->get('name/first'));
            $this->assertEquals('Fox', $row->get('name/last'));

            break;
        }
    }
    public function testCannotReadNonexistentCell()
    {
        foreach ($this->getTestCsv()->rows() as $row) {
            $exceptionCaught = false;

            try {
                $row['name/middle'];
            } catch (\OutOfBoundsException $e) {
                $exceptionCaught = true;
            }

            $this->assertTrue($exceptionCaught);

            break;
        }
    }

    public function testGetFromNonexistentCellReturnsDefault()
    {
        foreach ($this->getTestCsv()->rows() as $row) {
            $this->assertNull($row->get('name/middle', null));

            break;
        }
    }

    public function testCheckCellExistence()
    {
        foreach ($this->getTestCsv()->rows() as $row) {
            $this->assertTrue(isset($row['name/first']));
            $this->assertTrue(isset($row['name/last']));
            $this->assertFalse(isset($row['name/middle']));

            break;
        }
    }

    public function testConvertRowToArray()
    {
        foreach ($this->getTestCsv()->rows() as $row) {
            $arr = $row->toArray();

            $this->assertEquals('Birdie', $arr['name/first']);
            $this->assertEquals('Fox', $arr['name/last']);

            break;
        }
    }
}

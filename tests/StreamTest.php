<?php

namespace BYanelli\CsvQuery\Tests;

use BYanelli\CsvQuery\Tests\Concerns\LocatesResources;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    use LocatesResources;

    public function testSeekToLine()
    {
        $line = $this->getTestCsvStream()->line(2);

        $this->assertTrue(Str::startsWith($line, '2,Willie,Rodriquez'));
    }

    public function testSeekToPreviousLine()
    {
        $this->getTestCsvStream()->line(2);
        $line = $this->getTestCsvStream()->line(1);

        $this->assertTrue(Str::startsWith($line, '1,Birdie,Fox'));
    }

    public function testCannotSeekToLineAfterEof()
    {
        $this->expectException(\Exception::class);

        $this->getTestCsvStream()->line(102);
    }

    public function testCrlfFileHasCarriageReturns()
    {
        $file = file_get_contents(__DIR__.'/resources/test_crlf.csv');

        $this->assertNotFalse(strpos($file, "\r"));
    }

    public function testCrlfSeekToLine()
    {
        $line = $this->getTestCsvCrlfStream()->line(2);

        // todo: why does this work????

        $this->assertFalse(strpos($line, "\r"));

        $this->assertTrue(Str::startsWith($line, '2,Willie,Rodriquez'));
    }

    public function testCrlfSeekToPreviousLine()
    {
        $this->getTestCsvCrlfStream()->line(2);
        $line = $this->getTestCsvCrlfStream()->line(1);

        $this->assertFalse(strpos($line, "\r"));

        $this->assertTrue(Str::startsWith($line, '1,Birdie,Fox'));
    }

    public function testCrlfCannotSeekToLineAfterEof()
    {
        $this->expectException(\Exception::class);

        $this->getTestCsvCrlfStream()->line(102);
    }
}

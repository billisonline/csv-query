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
}

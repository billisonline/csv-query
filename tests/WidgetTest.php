<?php

namespace BYanelli\CsvQuery\Tests;

use BYanelli\CsvQuery\Widget;
use PHPUnit\Framework\TestCase;

class WidgetTest extends TestCase
{
    public function testSomething()
    {
        $widget = new Widget();
        $this->assertTrue($widget->doSomething());
    }
}

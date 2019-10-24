<?php

namespace BYanelli\CsvQuery;

use GuzzleHttp\Stream\Stream as BaseStream;
use GuzzleHttp\Stream\StreamDecoratorTrait;
use GuzzleHttp\Stream\StreamInterface;
use GuzzleHttp\Stream\Utils;

class Stream implements StreamInterface
{
    use StreamDecoratorTrait;

    public static function make($resource = '', array $options = [])
    {
        if (is_string($resource)) {
            $resource = fopen($resource, 'r');
        }

        return new static(BaseStream::factory($resource, $options));
    }

    public function readLine()
    {
        return trim(Utils::readLine($this->stream));
    }
}

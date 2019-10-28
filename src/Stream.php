<?php

namespace BYanelli\CsvQuery;

use GuzzleHttp\Stream\Stream as BaseStream;
use GuzzleHttp\Stream\StreamDecoratorTrait;
use GuzzleHttp\Stream\StreamInterface;
use GuzzleHttp\Stream\Utils;

class Stream implements StreamInterface
{
    use StreamDecoratorTrait;

    /**
     * @var resource
     */
    private $handle;

    public static function make($resource = '', array $options = [])
    {
        if (is_string($resource)) {
            $resource = fopen($resource, 'r');
        }

        return new static($resource, BaseStream::factory($resource, $options));
    }

    public function __construct($handle, StreamInterface $stream)
    {
        $this->handle = $handle;
        $this->stream = $stream;
    }

    public function readLine(): string
    {
        return trim(Utils::readLine($this->stream));
    }

    public function rewind()
    {
        return rewind($this->handle);
    }
}

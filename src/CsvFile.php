<?php

namespace BYanelli\CsvQuery;


class CsvFile
{
    /**
     * @var string|null
     */
    protected $fromPath = null;

    /**
     * @var Stream
     */
    protected $stream;

    /**
     * @param string|resource $file
     */
    public function __construct($file)
    {
        if (is_string($file)) {
            $this->fromPath = $file;
        }

        $this->stream = Stream::make($file);
    }

    protected function isFromPath(): bool
    {
        return !is_null($this->fromPath);
    }

    public function test()
    {
        while ($line = $this->stream->readLine()) {
            echo $line;
            break;
        }
    }
}

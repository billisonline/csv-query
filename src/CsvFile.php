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
     * @var string
     */
    private $separator;

    /**
     * @var array
     */
    private $headers = [];

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

    public function lines()
    {
        $this->stream->rewind();

        $this->parseHeaders($this->stream->readLine());

        while (!$this->stream->eof()) {
            $line = $this->stream->readLine();

            if ($line == '') {continue;}

            yield $line;
        }
    }

    /**
     * @return CsvRow[]|\Generator
     */
    public function rows()
    {
        foreach ($this->lines() as $line) {
            yield new CsvRow($line, $this);
        }
    }

    private function parseHeaders(string $line)
    {
        if (is_null($this->separator)) {
            $this->separator = ','; //$this->detectSeparator($line);
        }

        if (empty($this->headers)) {
            $this->headers = str_getcsv($line, $this->separator);
        }
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }
}

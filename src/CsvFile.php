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
        foreach ($this->stream->lines() as $lineNumber => $line) {
            yield $lineNumber => $line;
        }
    }

    public function line(int $number)
    {
        return $this->stream->line($number);
    }

    /**
     * @return CsvRow[]|\Generator
     */
    public function rows()
    {
        $first = true;

        foreach ($this->lines() as $lineNumber => $line) {
            if ($first) {
                $this->parseHeadersAndSeparator($line);

                $first = false;

                continue;
            }

            $rowNumber = $this->lineNumberToRowNumber($lineNumber);

            yield $rowNumber => new CsvRow($line, $this);
        }
    }

    public function row(int $number): CsvRow
    {
        $this->parseHeadersAndSeparatorIfNotParsed();

        $line = $this->stream->line($this->rowNumberToLineNumber($number));

        return new CsvRow($line, $this);
    }

    private function lineNumberToRowNumber(int $lineNumber): int
    {
        return $lineNumber - 1;
    }

    private function rowNumberToLineNumber(int $rowNumber): int
    {
        return $rowNumber + 1;
    }

    private function parseHeadersAndSeparator(string $line)
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
        $this->parseHeadersAndSeparatorIfNotParsed();

        return $this->headers;
    }

    public function getSeparator(): string
    {
        $this->parseHeadersAndSeparatorIfNotParsed();

        return $this->separator;
    }

    private function parseHeadersAndSeparatorIfNotParsed(): void
    {
        if (!empty($this->separator) && !empty($this->headers)) {return;}

        foreach ($this->rows() as $row) {break;}
    }
}

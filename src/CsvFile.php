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

    public function line(int $number): string
    {
        return $this->stream->line($number);
    }

    /**
     * @return CsvRow[]|\Generator
     */
    public function iterateRows()
    {
        $this->ensureHeadersParsed();

        foreach ($this->stream->linePositions() as $lineNumber => $position) {
            if ($lineNumber == 0) {continue;} // Skip headers

            $rowNumber = $this->lineNumberToRowNumber($lineNumber);

            yield $rowNumber => new CsvRow($this, $rowNumber);
        }
    }

    public function rows(): CsvRowCollection
    {
        return new CsvRowCollection($this);
    }

    public function row(int $number): CsvRow
    {
        $this->ensureHeadersParsed();

        $this->validateRowNumber($number);

        return new CsvRow($this, $number);
    }

    private function lineNumberToRowNumber(int $lineNumber): int
    {
        return $lineNumber - 1;
    }

    private function rowNumberToLineNumber(int $rowNumber): int
    {
        return $rowNumber + 1;
    }

    private function parseHeaders()
    {
        $line = $this->line(0);

        if (is_null($this->separator)) {
            $this->separator = ','; //$this->detectSeparator($line);
        }

        if (empty($this->headers)) {
            $this->headers = str_getcsv($line, $this->separator);
        }
    }

    public function headers(): array
    {
        $this->ensureHeadersParsed();

        return $this->headers;
    }

    public function separator(): string
    {
        $this->ensureHeadersParsed();

        return $this->separator;
    }

    private function ensureHeadersParsed(): void
    {
        if (!$this->headersParsed()) {$this->parseHeaders();}
    }

    public function lineForRow(int $rowNumber): string
    {
        return $this->line($this->rowNumberToLineNumber($rowNumber));
    }

    private function headersParsed(): bool
    {
        return !empty($this->separator) && !empty($this->headers);
    }

    private function validateRowNumber(int $number)
    {
        foreach ($this->stream->linePositions() as $i => $position) {
            if ($i >= $number) {
                return;
            }
        }

        throw new \Exception('Invalid row number');
    }
}

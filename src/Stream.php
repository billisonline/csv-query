<?php

namespace BYanelli\CsvQuery;

use GuzzleHttp\Stream\Stream as BaseStream;
use GuzzleHttp\Stream\StreamDecoratorTrait;
use GuzzleHttp\Stream\StreamInterface;
use GuzzleHttp\Stream\Utils;

class Stream implements StreamInterface
{
    use StreamDecoratorTrait;

    const POSITION_ERROR = -1;

    /**
     * @var resource
     */
    private $handle;

    /**
     * @var int[]|array
     */
    private $linePositions = [];

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

    public function position(): int
    {
        $position = $this->tell();

        if ($position === false) {return self::POSITION_ERROR;}

        return $position;
    }

    public function hasMovedFrom(int $position): bool
    {
        return $this->position() != $position;
    }

    public function lines()
    {
        $currentPosition = null;

        $lineNumber = 0;

        $this->rewind();

        while (!$this->eof()) {
            // Restore position if our pointer was moved since the last iteration
            if (
                !is_null($currentPosition)
                && ($this->hasMovedFrom($currentPosition))
            ) {
                $this->seek($currentPosition);
            }

            // Mark position at the beginning of this line
            $this->markLinePosition($lineNumber);

            $line = $this->readLine();

            // Set current position at the beginning of the next line
            $currentPosition = $this->position();

            // Terminate if we have reached a position in error
            if ($this->position() === self::POSITION_ERROR) {break;}

            // Skip blank lines and don't increment line number
            if (empty($line)) {continue;}

            yield $lineNumber => $line;

            $lineNumber++;
        }
    }

    private function seekToLine(int $lineNumber): void
    {
        if ($lineNumber < 0) {
            throw new \Exception('Line number cannot be negative');
        }

        // Seek to stored line position if available
        if (!is_null($position = $this->linePositions[$lineNumber] ?? null)) {
            $this->seek($position);
            return;
        }

        foreach ($this->lines() as $i => $line) {
            if (($i + 1) === $lineNumber) {
                return;
            }
        }

        throw new \Exception('Invalid line number');
    }

    public function line(int $lineNumber): string
    {
        $this->seekToLine($lineNumber);

        return $this->readLine();
    }

    private function markLinePosition(int $lineNumber)
    {
        $this->linePositions[$lineNumber] = $this->position();
    }
}

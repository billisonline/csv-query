<?php

namespace BYanelli\CsvQuery;

use GuzzleHttp\Stream\Stream as BaseStream;
use GuzzleHttp\Stream\StreamDecoratorTrait;
use GuzzleHttp\Stream\StreamInterface;
use GuzzleHttp\Stream\Utils;

class Stream implements StreamInterface
{
    use StreamDecoratorTrait, InteractsWithStreams;

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

    public function seekBefore(int $position)
    {
        $this->stream->seek($position-1);
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
        foreach ($this->linePositions() as $lineNumber => $position) {
            yield $lineNumber => $this->readLine();

            $this->stream->seek($position);
        }
    }

    public function linePositions()
    {
        $currentPosition = 0;
        $lineNumber = 0;
        $skipNext = false;
        $terminateNext = false;

        $this->rewind();

        while (true) {
            if ($skipNext) {$skipNext = false; continue;}
            if ($terminateNext) {break;}

            // Mark position at the beginning of this line
            $this->markLinePosition($lineNumber, $currentPosition);

            yield $lineNumber => $currentPosition;

            $lineNumber++;

            // If we've already saved the next line position, don't search for it again
            if (!is_null($savedPosition = $this->linePositions[$lineNumber] ?? null)) {
                $currentPosition = $savedPosition;

                continue;
            }

            // Restore position in case our pointer was moved since the last iteration
            $this->seek($currentPosition);

            // Advance to next line break; record whether line was blank or last line
            [$lineHasContent, $isLastLine] = $this->advanceLine($this->stream);

            // Set current position at the beginning of the next line
            $currentPosition = $this->position();

            // On the next iteration, skip if the line had no content
            $skipNext = !$lineHasContent;

            // On the next iteration, terminate if we reached the last line, EOF, or an error position
            $terminateNext = $isLastLine || $this->eof() || ($currentPosition === self::POSITION_ERROR);
        }
    }

    private function seekToLine(int $lineNumber): void
    {
        if ($lineNumber == 0) {
            $this->rewind();
            return;
        }

        if ($lineNumber < 0) {
            throw new \Exception('Line number cannot be negative');
        }

        // Seek to stored line position if available
        if (!is_null($position = $this->linePositions[$lineNumber] ?? null)) {
            $this->seekBefore($position);
            return;
        }

        foreach ($this->linePositions() as $i => $position) {
            if ($i == $lineNumber) {
                $this->seekBefore($position);
                return;
            }
        }

        throw new \Exception("Invalid line number {$i}");
    }

    public function line(int $lineNumber): string
    {
        $this->seekToLine($lineNumber);

        return $this->readLine();
    }

    private function markLinePosition(int $lineNumber, int $position=-1)
    {
        $this->linePositions[$lineNumber] = ($position > -1)? $position : $this->position();
    }
}

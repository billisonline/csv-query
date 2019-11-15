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

    /**
     * @var int[]|array
     */
    private $lineLengths = [];

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

    public function readLine(int $length=0): string
    {
        $ret = '';

        if ($length) {
            return $this->read($length);
        }

        $bufferSize = 512;

        while (true) {
            $buffer = $this->read($bufferSize);

            $lineBreakLocation = strpos($buffer, "\n");

            if (($lineBreakLocation === false) && $this->eof()) {
                $ret = $buffer;

                break;
            }

            if ($lineBreakLocation === false) {
                $ret .= $buffer;
            } else {
                $ret .= substr($buffer, 0, $lineBreakLocation);

                break;
            }
        }

        return $ret;
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
        foreach ($this->linePositions() as $lineNumber => [$position, $length]) {
            yield $lineNumber => $this->readLine($length);

            $this->stream->seek($position);
        }
    }

    public function linePositions()
    {
        $buffer = '';
        $bufferSize = 512;
        $streamSize = $this->getSize();

        $currentPosition = 0;
        $lineBreakLocation = 0;
        $lineNumber = 0;
        $skipNext = false;

        $this->rewind();

        while (true) {
            if ($skipNext) {
                continue;
            }

            if ($this->eof()) {
                if (empty(trim($buffer))) {
                    break;
                }

                if ($bufferSize > 1) {
                    $bufferSize = $bufferSize / 2;
                    $this->seek($currentPosition);
                    continue;
                } else {
                    break;
                }
            }

            if ($lineBreakLocation !== false) {
                if (($currentPosition + 1) >= $streamSize) {
                    break;
                }

                $this->seek($currentPosition);

                $this->markLinePosition($lineNumber, $currentPosition);
                $this->markLineLength($lineNumber, $lineBreakLocation);

                yield $lineNumber => [$currentPosition, $lineBreakLocation];

                $lineNumber++;
            }

            $this->seek($currentPosition);

            $buffer = $this->read($bufferSize);

            $lineBreakLocation = strpos($buffer, "\n");

            if ($lineBreakLocation !== false) {
                $currentPosition += $lineBreakLocation + 1;
            } else {
                $currentPosition += $bufferSize;
            }
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
        if (!is_null($position = $this->getLinePosition($lineNumber))) {
            $this->seek($position);
            return;
        }

        foreach ($this->linePositions() as $i => [$position, $length]) {
            if ($i == $lineNumber) {
                $this->seek($position);
                return;
            }
        }

        throw new \Exception("Invalid line number {$i}");
    }

    public function line(int $lineNumber): string
    {
        $this->seekToLine($lineNumber);

        if (!is_null($length = $this->getLineLength($lineNumber))) {
            return $this->readLine($length);
        }

        return $this->readLine();
    }

    private function markLinePosition(int $lineNumber, int $position)
    {
        $this->linePositions[$lineNumber] = $position;
    }

    private function markLineLength(int $lineNumber, int $length)
    {
        $this->lineLengths[$lineNumber] = $length;
    }

    private function getLinePosition(int $lineNumber): ?int
    {
        return $this->linePositions[$lineNumber] ?? null;
    }

    private function getLineLength(int $lineNumber): ?int
    {
        return $this->lineLengths[$lineNumber] ?? null;
    }
}

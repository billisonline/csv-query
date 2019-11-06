<?php

namespace BYanelli\CsvQuery;

use GuzzleHttp\Stream\StreamInterface;

/**
 * @property StreamInterface stream
 */
trait InteractsWithStreams
{
    public function advanceLine(StreamInterface $stream): array
    {
        $isLastCharacter = false;
        $isLastLine = false;
        $hasContent = false;

        while (true) {
            // Read the next byte; record whether it's EOF
            $isLastLine = (false === ($byte = $stream->read(1)));

            // Break if we found EOL or EOF
            if ($isLastCharacter || $isLastLine) {
                //$stream->seek(-1, SEEK_CUR); // Rewind?
                break;
            }

            // Mark this line as having content if a non-space character is found
            if (!$hasContent && preg_match('/[^\\s]+/', $byte)) {
                $hasContent = true;
            }

            // Record whether we found EOL
            if ($byte == PHP_EOL) {$isLastCharacter = true;}
        }

        return [$hasContent, $isLastLine];
    }

}

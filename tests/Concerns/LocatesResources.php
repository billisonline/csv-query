<?php


namespace BYanelli\CsvQuery\Tests\Concerns;


use BYanelli\CsvQuery\CsvFile;
use BYanelli\CsvQuery\Stream;

trait LocatesResources
{
    protected function resourcesPath(string $file='')
    {
        return join(DIRECTORY_SEPARATOR, array_filter([
            __DIR__,
            '..',
            'resources',
            $file
        ]));
    }

    protected function getTestCsvStream(): Stream
    {
        return Stream::make($this->resourcesPath('test.csv'));
    }

    protected function getTestCsvCrlfStream(): Stream
    {
        return Stream::make($this->resourcesPath('test_crlf.csv'));
    }

    protected function getTestCsv(): CsvFile
    {
        return new CsvFile($this->resourcesPath('test.csv'));
    }
}

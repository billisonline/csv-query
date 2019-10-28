<?php


namespace BYanelli\CsvQuery\Tests\Concerns;


use BYanelli\CsvQuery\CsvFile;

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

    protected function getTestCsv(): CsvFile
    {
        return new CsvFile($this->resourcesPath('test.csv'));
    }
}

<?php

namespace BYanelli\CsvQuery;

use Illuminate\Support\LazyCollection;

class CsvRowCollection extends LazyCollection
{
    public function __construct($source)
    {
        if ($source instanceof CsvFile) {
            $source = $this->makeCsvRowsIterator($source);
        }

        parent::__construct($source);
    }

    private function makeCsvRowsIterator(CsvFile $file): \Closure
    {
        return function () use ($file) {yield from $file->iterateRows();};
    }
}

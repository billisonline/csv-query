<?php

namespace BYanelli\CsvQuery;

use Illuminate\Contracts\Support\Arrayable;

class CsvRow implements \ArrayAccess, Arrayable
{
    /**
     * @var string
     */
    private $line;

    /**
     * @var CsvFile
     */
    private $file;

    /**
     * @var array
     */
    private $cells;

    public function __construct(string $line, CsvFile $file)
    {
        $this->line = $line;
        $this->file = $file;

        $this->cells = $this->parseRow();
    }

    private function parseRow(): array
    {
        return str_getcsv($this->line, $this->file->getSeparator());
    }

    public function offsetExists($offset)
    {
        return array_search($offset, $this->file->getHeaders()) !== false;
    }

    public function offsetGet($offset)
    {
        if (!is_string($offset)) {
            throw new \Exception;
        }

        $index = array_search($offset, $this->file->getHeaders());

        if (!$index) {
            throw new \OutOfBoundsException;
        }

        return $this->cells[$index];
    }

    public function get(string $name, $default = null)
    {
        $index = array_search($name, $this->file->getHeaders());

        if (!$index) {
            return $default;
        }

        return $this->cells[$index];
    }

    public function offsetSet($offset, $value)
    {
        throw new \Exception('not implemented');
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('not implemented');
    }

    public function toArray()
    {
        return array_combine($this->file->getHeaders(), $this->cells);
    }
}

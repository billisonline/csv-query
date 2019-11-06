<?php

namespace BYanelli\CsvQuery;

use Illuminate\Contracts\Support\Arrayable;

class CsvRow implements \ArrayAccess, Arrayable
{
    /**
     * @var CsvFile
     */
    private $file;

    /**
     * @var int
     */
    private $rowNumber;

    /**
     * @var string
     */
    private $line = '';

    /**
     * @var array
     */
    private $cells = [];

    public function __construct(CsvFile $file, int $rowNumber)
    {
        $this->file = $file;
        $this->rowNumber = $rowNumber;
    }

    private function line(): string
    {
        if (empty($this->line)) {
            $this->line = $this->file->lineForRow($this->rowNumber);
        }

        return $this->line;
    }

    private function cells(): array
    {
        if (empty($this->cells)) {
            $this->cells = str_getcsv($this->line(), $this->file->separator());
        }

        return $this->cells;
    }

    public function offsetExists($offset)
    {
        return array_search($offset, $this->file->headers()) !== false;
    }

    public function offsetGet($offset)
    {
        if (!is_string($offset)) {
            throw new \Exception;
        }

        $index = array_search($offset, $this->file->headers());

        if (!$index) {
            throw new \OutOfBoundsException;
        }

        return $this->cells()[$index];
    }

    public function get(string $name, $default = null)
    {
        $index = array_search($name, $this->file->headers());

        if ($index === false) {
            return $default;
        }

        return $this->cells()[$index];
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
        return array_combine($this->file->headers(), $this->cells());
    }
}

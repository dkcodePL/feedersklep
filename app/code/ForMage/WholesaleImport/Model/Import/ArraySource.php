<?php

namespace ForMage\WholesaleImport\Model\Import;

use Magento\ImportExport\Model\Import\AbstractSource;

class ArraySource extends AbstractSource
{
    /**
     *
     * @var \ArrayIterator
     */
    protected $iterator;

    /**
     * ArrayAdapter constructor.
     * @param $data
     */
    public function __construct(&$data)
    {
        $this->iterator = new \ArrayIterator($data);
        parent::__construct($this->prepareColNames());
    }

    protected function _getNextRow()
    {
        throw new \Exception('Get Next row not implemented, but required by AbstractSource');
    }

    /**
     * @return array
     */
    protected function prepareColNames()
    {
        $keys = [];
        foreach ($this->iterator as $item) {
            foreach (array_keys($item) as $colName) {
                $keys[$colName] = $colName;
            }
        }
        $this->rewind();

        return array_values($keys);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->iterator->current();
    }

    public function next()
    {
        $this->iterator->next();
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        $this->iterator->rewind();
    }

    /**
     * @param $position
     */
    public function seek($position)
    {
        $this->iterator->seek($position);
    }
}

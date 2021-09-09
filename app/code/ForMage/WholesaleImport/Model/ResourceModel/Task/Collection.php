<?php

namespace ForMage\WholesaleImport\Model\ResourceModel\Task;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ForMage\WholesaleImport\Model\ResourceModel\Task as TaskResource;
use ForMage\WholesaleImport\Model\Task;

/**
 * Class Collection
 * @package ForMage\WholesaleImport
 */
class Collection extends AbstractCollection
{

    protected $_idFieldName = 'id';
    /**
     * Set resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Task::class, TaskResource::class);
    }
}
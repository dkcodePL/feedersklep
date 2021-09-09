<?php

namespace ForMage\WholesaleImport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Wholesale resource
 */
class Task extends AbstractDb
{
    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('4mage_wholesaleimport_task', 'id');
    }
}
<?php

namespace ForMage\WholesaleImport\Model;

use Magento\Framework\Model\AbstractModel;

class Task extends AbstractModel {


    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
        $this->_init(ResourceModel\Task::class);
    }

}

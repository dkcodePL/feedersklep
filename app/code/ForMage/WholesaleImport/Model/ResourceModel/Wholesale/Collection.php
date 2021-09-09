<?php

namespace ForMage\WholesaleImport\Model\ResourceModel\Wholesale;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ForMage\WholesaleImport\Model\ResourceModel\Wholesale as WholesaleResource;
use ForMage\WholesaleImport\Model\Wholesale;

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
        $this->_init(Wholesale::class, WholesaleResource::class);
    }

    public function isActive()
    {
        return $this
            ->addFieldToFilter('is_active', 1);
    }
}
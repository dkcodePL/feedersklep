<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;

class Slide extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @var array
     */
    protected $_fields = [
        'store_ids', 'group_ids'
    ];

    /**
     * Slide constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    )
    {
        parent::__construct($context);
        $this->datetime = $dateTime;
    }

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('4mage_slide', 'slide_id');
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        foreach ($this->_fields as $field) {
            $object->setData($field, explode(',', $object->getData($field)));
        }

        return parent::_afterLoad($object);
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->datetime->gmtDate());
        }
        if (is_array($object->getStoreIds())) {
            if (in_array(Store::DEFAULT_STORE_ID, $object->getStoreIds())) {
                $object->setStoreIds([Store::DEFAULT_STORE_ID]);
            }
        }

        foreach ($this->_fields as $field) {
            $object->setData($field, implode(',', $object->getData($field)));
        }

        return parent::_afterLoad($object);
    }

}
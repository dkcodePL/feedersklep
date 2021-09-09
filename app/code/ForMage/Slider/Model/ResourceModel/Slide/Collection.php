<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Model\ResourceModel\Slide;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'slide_id';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ForMage\Slider\Model\Slide', 'ForMage\Slider\Model\ResourceModel\Slide');
    }

    public function getActive()
    {
        return $this->addFieldToFilter('is_active', 1);
    }

    public function sortByPosition()
    {
        return $this->setOrder('position','ASC');
    }

    public function getByGroup($group, $storeId = 0)
    {
        $this
            ->getActive()
            ->sortByPosition()
            ->addFieldToFilter('group_ids', ['finset' => $group]);

        if ($storeId) {
            return $this->addFieldToFilter('store_ids', [
                ['finset' => \ForMage\Slider\Ui\Component\Listing\Column\Slide\Store\Options::ALL_STORE_VIEWS],
                ['finset' => $storeId]
            ]);
        }

        return $this
            ->addFieldToFilter('store_ids', ['finset' => \ForMage\News\Ui\Component\Listing\Column\News\Store\Options::ALL_STORE_VIEWS]);

    }

}

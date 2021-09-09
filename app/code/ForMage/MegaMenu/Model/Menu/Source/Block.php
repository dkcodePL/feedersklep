<?php
/**
 * @copyright Copyright (c) 2018 - 2020 adam@intw.pl
 */

namespace ForMage\MegaMenu\Model\Menu\Source;

use Magento\Framework\Data\OptionSourceInterface;


class Block implements OptionSourceInterface
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\Collection
     */
    protected $_collection;

    /**
     * Block constructor.
     * @param \Magento\Cms\Model\ResourceModel\Block\Collection $collection
     */
    public function __construct(
        \Magento\Cms\Model\ResourceModel\Block\Collection $collection
    )
    {
        $this->_collection = $collection;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        return $this->_collection->toOptionArray();
    }

}

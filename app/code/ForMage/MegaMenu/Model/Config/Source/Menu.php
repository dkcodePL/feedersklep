<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Model\Config\Source;

class Menu implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \ForMage\MegaMenu\Model\ResourceModel\Menu\Collection
     */
    protected $_collectionFactory;

    /**
     * Menu constructor.
     * @param \ForMage\MegaMenu\Model\ResourceModel\Menu\CollectionFactory $collectionFactory
     */
    public function __construct(
        \ForMage\MegaMenu\Model\ResourceModel\Menu\CollectionFactory $collectionFactory
    )
    {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $items = [];
        foreach ($this->toArray() as $id =>  $item) {
            $items[] = [
                'label' => $item,
                'value' => $id,
            ];
        }

        return $items;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $items = [];

        $collection = $this->_collectionFactory->create()->getMenu();
        foreach ($collection as $item) {
            $items[$item->getId()] = $item->getName();
        }
        return $items;
    }
}

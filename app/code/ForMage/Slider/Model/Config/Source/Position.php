<?php
/**
 * @copyright Copyright (c) 2018 - 2020 adam@intw.pl
 */

namespace ForMage\Slider\Model\Config\Source;

class Position implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \ForMage\Slider\Helper\Data
     */
    protected $_helper;

    public function __construct(
        \ForMage\Slider\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
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
        return $this->_helper->getSlidePositions();
    }
}

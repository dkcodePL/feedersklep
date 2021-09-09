<?php

namespace ForMage\WholesaleImport\Model\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_PRODUCTS = 'products';
    const TYPE_IMAGES = 'images';
    const TYPE_DELETE = 'delete';

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
        return [
            self::TYPE_PRODUCTS => __('Import Products'),
            self::TYPE_DELETE => __('Delete Products'),
            self::TYPE_IMAGES => __('Import Products Images'),
        ];
    }
}

<?php

namespace ForMage\WholesaleImport\Model\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETE = 'complete';
    const STATUS_ERROR = 'error';
    const STATUS_CANCELED = 'canceled';

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
            self::STATUS_ERROR => __('Error'),
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_COMPLETE => __('Complete'),
            self::STATUS_CANCELED => __('Canceled'),
        ];
    }
}

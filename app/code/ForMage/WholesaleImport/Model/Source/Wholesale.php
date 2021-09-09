<?php

namespace ForMage\WholesaleImport\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use ForMage\WholesaleImport\Helper\Data as Helper;

class Wholesale implements OptionSourceInterface
{

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Manufacturer constructor.
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray() {

        $options = [];
        $options[] = [
            'label' => '---',
            'value' => ''
        ];

        foreach ($this->helper->getProductAttributeOptions('wholesale') as $attribute) {

            $options[] = [
                'label' => $attribute->getLabel(),
                'value' => $attribute->getValue()
            ];
        }

        return $options;
    }

}
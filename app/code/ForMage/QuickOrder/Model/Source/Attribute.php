<?php
/*
 *  4mage Package
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the 4mage.co license that is
 *  available through the world-wide-web at this URL:
 *  https://4mage.co/license-agreement/
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade this extension to newer
 *  version in the future.
 *
 *  @category 	ForMage
 *  @package 	ForMage_QuickOrder
 *  @copyright 	Copyright (c) 2020 4mage.co (https://4mage.co/)
 *  @license  	https://4mage.co/license-agreement/
 *
 */

namespace ForMage\QuickOrder\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Attribute implements OptionSourceInterface
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * Attribute constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributecollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributecollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributecollectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray() {

        $options = [];
        foreach ($this->getAllAttributes() as $attributeCode => $attributeLabel) {

            $options[] = [
                'label' => $attributeLabel . ' (' . $attributeCode . ')',
                'value' => $attributeCode
            ];
        }

        return $options;
    }

    public function getAllAttributes()
    {
        $data = [];
        foreach ($this->_getAllAttributes() as $attribute) {

            if (!$attribute->getFrontendLabel()) continue;

            $data[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        return $data;
    }

    protected function _getAllAttributes()
    {
        $attributeCollection = $this->attributeCollectionFactory->create();

        return $attributeCollection->getItems();
    }
}
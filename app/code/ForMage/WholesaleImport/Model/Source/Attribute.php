<?php

namespace ForMage\WholesaleImport\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Attribute implements OptionSourceInterface
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    protected $replaceCodes = [
        'image' => 'base_image',
        'thumbnail' => 'thumbnail_image',
        'thumbnail_label' => 'thumbnail_image_label',
        'image_label' => 'base_image_label',
        'quantity_and_stock_status' => 'qty',
        'tax_class_id' => 'tax_class_name',
        'category_ids' => 'categories',
        'status' => 'product_online',
    ];

    /**
     * Attribute constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
    )
    {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        $options = [];
        $options[] = [
            'label' => '---',
            'value' => ''
        ];

        $options[] = [
            'label' => __('Attribute Set Code') . ' (attribute_set_code)',
            'value' => 'attribute_set_code'
        ];

        $options[] = [
            'label' => __('Product Websites') . ' (product_websites)',
            'value' => 'product_websites'
        ];

        $options[] = [
            'label' => __('Upsell Skus') . ' (upsell_skus)',
            'value' => 'upsell_skus'
        ];

        $options[] = [
            'label' => __('Related Skus') . ' (related_skus)',
            'value' => 'related_skus'
        ];

        $options[] = [
            'label' => __('CrossSell Skus') . ' (crosssell_skus)',
            'value' => 'crosssell_skus'
        ];

        foreach ($this->getAllAttributes() as $attribute) {

            $label = $attribute->getFrontendLabel();
            if (!strlen($label)) continue;

            $attributeCode = $attribute->getAttributeCode();
            if (in_array($attributeCode, array_flip($this->replaceCodes))) {
                $attributeCode = $this->replaceCodes[$attributeCode];
            }

            $options[] = [
                'label' => $attribute->getFrontendLabel() . ' (' . $attributeCode . ')',
                'value' => $attributeCode
            ];
        }
        return $options;
    }

    protected function getAllAttributes()
    {
        $attributeCollection = $this->attributeCollectionFactory->create();

        return $attributeCollection->getItems();
    }
}
<?php

namespace ForMage\FeederTheme\Block\Product;

use ForMage\FeederTheme\Helper\Data as Helper;

class Manufacturer extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry = null;

    /**
     * @var Product
     */
    private $_product = null;

    /**
     * @var Helper
     */
    private $_helper;

    /**
     * Manufacturer constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        Helper $helper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }


    public function getSearchLink()
    {
        $product = $this->getProduct();
        $category = $this->getCategoryByManufacturerId($product->getManufacturer());

        return $category && $category->getId() ? $category->getUrl() : $this->getUrl('catalogsearch/result', ['_query' => 'q=' . str_replace(' ', '+', $product->getAttributeText('manufacturer'))]);
    }

    public function getSwatchImage()
    {
        $product = $this->getProduct();
        return $this->_helper->getSwatchImage($product);
    }

    protected function getCategoryByManufacturerId($manufacturerId)
    {
        return $this->_helper->getCategoryCollection()
            ->addFieldToSelect(['name', 'url_key', 'manufacturer'])
            ->addFieldToFilter('manufacturer', $manufacturerId)
            ->getFirstItem();

    }

    public function getProductCategories($product)
    {
        return $this->_helper->getCategoryCollection()
            ->addFieldToSelect(['name', 'url_key', 'manufacturer'])
            ->addFieldToFilter('manufacturer', ['null' => true])
            ->addIdFilter($product->getCategoryIds() ?? [0]);

    }

    public function getProductCategory($product)
    {
        $category = $product->getCategory();

        return $category && $category->getId() ? $category : $this->getProductCategories($product)->getFirstItem();

    }


}
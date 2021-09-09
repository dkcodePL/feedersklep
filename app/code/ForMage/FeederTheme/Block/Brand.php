<?php

namespace ForMage\FeederTheme\Block;

use ForMage\FeederTheme\Helper\Data as Helper;

class Brand extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $_helper;

    /**
     * Brand constructor.
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

    public function getBrands()
    {
        $collection = $this->_helper->getCategoryCollection();
        $collection->addFieldToSelect(['name', 'url_key', 'manufacturer']);
        $collection->addFieldToFilter('manufacturer', ['neq' => 'NULL']);

        return $collection;
    }

    public function getBrandsByFirstLetter($collection)
    {
        $brands = [];
        $collection->setOrder('name', 'asc');
        foreach ($collection as $brand) {
            $brands[mb_substr($brand->getName(), 0, 1, "UTF-8")][] = $brand;
        }
        return $brands;
    }


    public function getBrandImage($category)
    {
        return $this->_helper->getSwatchImage($category);
    }


}
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

namespace ForMage\QuickOrder\Block\Product;

use ForMage\QuickOrder\Helper\Data as Helper;

class Index extends \Magento\Catalog\Block\Product\AbstractProduct implements
    \Magento\Framework\DataObject\IdentityInterface
{

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\View\Element\Html\Select
     */
    protected $_select;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \ForMage\QuickOrder\Model\Source\Attribute
     */
    protected $attribute;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * Index constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\View\Element\Html\Select $select
     * @param \ForMage\QuickOrder\Model\Source\Attribute $attribute
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\View\Element\Html\Select $select,
        \ForMage\QuickOrder\Model\Source\Attribute $attribute,
        Helper $helper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_helper = $helper;
        $this->urlHelper = $urlHelper;
        $this->formKey = $formKey;
        $this->_select = $select;
        $this->attribute = $attribute;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }

    /**
     * @return sring
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getAttributesLabels()
    {
        $labels = [];

        $attributes = $this->attribute->getAllAttributes();

        foreach ($this->_helper->getProductAttributes() as $attributeCode) {

            if (isset($attributes[$attributeCode])) {
                $labels[] = $attributes[$attributeCode];
            }
        }

        return $labels;
    }

    public function getSelect($name, $options, $class = '', $extra = '')
    {
        $select = $this->_select->setOptions($options);
        $select->setId($name);
        $select->setName($name);
        $select->setClass($class);
        $select->setExtraParams($extra);

        return $select;
    }

    public function getAttributeSelect($attributeCode)
    {
        $options = $this->_helper->getProductAttributeOptions($attributeCode);
        return $this->getSelect($attributeCode, $options, 'field');
    }


}
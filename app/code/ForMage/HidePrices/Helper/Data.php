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
 *  @package 	ForMage_HidePrices
 *  @copyright 	Copyright (c) 2020 4mage.co (https://4mage.co/)
 *  @license  	https://4mage.co/license-agreement/
 *
 */

namespace ForMage\HidePrices\Helper;

use Magento\Framework\App\Http\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\ReadFactory;

class Data extends \ForMage\Base\Helper\Data
{
    const HIDE_PRICE = 'hide_price';
    const HIDE_ADD_TO_CART = 'hide_add_to_cart';

    const CONFIG_HIDE_PRICES_BASE_ENABLED = 'hideprices/base/enabled';
    const CONFIG_HIDE_PRICES_PRICE_ENABLED = 'hideprices/price/enabled';
    const CONFIG_HIDE_PRICES_PRICE_GROUP = 'hideprices/price/group';
    const CONFIG_HIDE_PRICES_CART_ENABLED = 'hideprices/cart/enabled';
    const CONFIG_HIDE_PRICES_CART_GROUP = 'hideprices/cart/group';


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig
     */
    protected $_scopeConfig;

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\ProductFactory
     */
    protected $productResource;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\ProductFactory $productResource
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param Context $httpContext
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productResource,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Context $httpContext
    )
    {
        parent::__construct($context);
        $this->httpContext = $httpContext;
        $this->_request = $request;
        $this->productRepository = $productRepository;
        $this->productResource = $productResource;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->storeManager = $storeManager;
    }

    /**
     * @param $attributeCode
     * @param $product
     * @param $defaultGroups
     * @return bool
     */
    public function canShow($attributeCode, $product, $defaultGroups)
    {
        $hidden = is_array($product->getData($attributeCode)) ? $product->getData($attributeCode) : explode(',', $product->getData($attributeCode));
        $hidden = array_filter($hidden, 'strlen');

        $currentCustomerGroup = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
        if (!empty($hidden) && in_array($currentCustomerGroup, $hidden) || empty($hidden) && in_array($currentCustomerGroup, $defaultGroups)) {
            return false;
        }
        return true;
    }

    protected function getProductAttributeValue($productId, $attributeCode)
    {
        return $this->productResource->create()->getAttributeRawValue($productId, $attributeCode, $this->storeManager->getStore());
    }

    /**
     * @param $saleableItem
     * @return bool
     */
    public function isPriceVisible($saleableItem)
    {
        if (!$this->getConfigValue(self::CONFIG_HIDE_PRICES_BASE_ENABLED) || !$this->getConfigValue(self::CONFIG_HIDE_PRICES_PRICE_ENABLED)) return true;

        $product = $this->getProductById($saleableItem->getId());

        return $this->canShow(self::HIDE_PRICE, $product, $this->getDefaultGroupsForPrices());
    }

    /**
     * @param $product
     * @return bool
     */
    public function isSaleable($product)
    {
        if (!$this->getConfigValue(self::CONFIG_HIDE_PRICES_BASE_ENABLED) || !$this->getConfigValue(self::CONFIG_HIDE_PRICES_CART_ENABLED)) return true;

        return $this->canShow(self::HIDE_ADD_TO_CART, $product, $this->getDefaultGroupsForCart());
    }

    public function isView($page)
    {
        return $this->_request->getFullActionName() === $page;
    }

    /**
     * @return array
     */
    public function getDefaultGroupsForPrices()
    {
        return array_filter(explode(',', $this->getConfigValue(self::CONFIG_HIDE_PRICES_PRICE_GROUP)), 'strlen');
    }

    /**
     * @return array
     */
    public function getDefaultGroupsForCart()
    {
        return array_filter(explode(',', $this->getConfigValue(self::CONFIG_HIDE_PRICES_CART_GROUP)), 'strlen');
    }

    public function getProductById($id)
    {
        try {
            return $this->productRepository->getById($id);

        } catch (\Exception $exception) {

            return false;
        }
    }

}

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

declare(strict_types=1);

namespace ForMage\HidePrices\Plugin;

use ForMage\HidePrices\Helper\Data as Helper;
use ForMage\HidePrices\Block\PriceBox;

/**
 * Class FinalPriceBox
 */
class FinalPriceBox
{
    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * FinalPriceBox constructor.
     * @param Helper $helper
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     */
    public function __construct(
        Helper $helper,
        \Magento\Framework\View\Element\BlockFactory $blockFactory
    )
    {
        $this->_helper = $helper;
        $this->_blockFactory = $blockFactory;
    }

    function aroundToHtml($subject, callable $proceed)
    {
        if (!$this->_helper->getConfigValue(Helper::CONFIG_HIDE_PRICES_PRICE_ENABLED)) return $proceed();

        $product = $this->_helper->getProductById($subject->getSaleableItem()->getId());

        if ($this->_helper->canShow(Helper::HIDE_PRICE, $product, $this->_helper->getDefaultGroupsForPrices())) return $proceed();

        $template = $subject->isProductList() ? PriceBox::TEMPLATE_PRODUCT_LIST : PriceBox::TEMPLATE_PRODUCT_VIEW;
        return $this->_blockFactory->createBlock('ForMage\HidePrices\Block\PriceBox')->setProduct($product)->setPriceHtml($proceed())->setTemplate($template)->toHtml();
    }
}


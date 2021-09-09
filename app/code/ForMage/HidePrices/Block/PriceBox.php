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

namespace ForMage\HidePrices\Block;

use \Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Context;

class PriceBox extends Template
{
    const TEMPLATE_PRODUCT_LIST = 'product/list/pricebox.phtml';
    const TEMPLATE_PRODUCT_VIEW = 'product/view/pricebox.phtml';

    /**
     * @var string
     */
    protected $_template = self::TEMPLATE_PRODUCT_LIST;


    public function toHtml()
    {
        return parent::toHtml();
    }


}
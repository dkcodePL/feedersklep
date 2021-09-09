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

use Magento\Catalog\Pricing\Price\RegularPrice as Price;
use ForMage\HidePrices\Helper\Data as Helper;

class RegularPrice extends \Magento\Framework\Pricing\Render
{

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * RegularPrice constructor.
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    )
    {
        $this->_helper = $helper;
    }


    public function afterGetValue(Price $subject, $result)
    {
        if ($this->_helper->isPriceVisible($subject->getProduct())) return $result;
        return false;
    }




}




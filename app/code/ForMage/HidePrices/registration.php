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

use \Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'ForMage_HidePrices',
    __DIR__
);

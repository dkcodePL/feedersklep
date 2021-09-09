<?php

/**
 * 4mage.co Package
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the 4mage.co license that is
 * available through the world-wide-web at this URL:
 * https://4mage.co/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	ForMage
 * @package 	ForMage_Base
 * @copyright 	Copyright (c) 2020 4mage.co (https://4mage.co/)
 * @license  	https://4mage.co/license-agreement/
 */

namespace ForMage\Base\Model;

use ForMage\Base\Helper\Data as Helper;

class Package
{
    /**
     * @var array
     */
    private $packages;

    /**
     * @var Helper
     */
    private $_helper;

    /**
     * Package constructor.
     * @param Helper $helper
     * @param array $packages
     */
    public function __construct(
        Helper $helper,
        array $packages = []
    )
    {
        $this->_helper = $helper;
        $this->packages = $packages;
    }

    public function getPackages()
    {
        return $this->packages;
    }


}

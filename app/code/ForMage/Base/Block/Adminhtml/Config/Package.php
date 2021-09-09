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

namespace ForMage\Base\Block\Adminhtml\Config;

class Package extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'ForMage_Base::config/package.phtml';

    /**
     * @var \ForMage\Base\Model\Package
     */
    private $package;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $objectFactory;

    /**
     * Package constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\DataObjectFactory $objectFactory
     * @param \ForMage\Base\Model\Package $package
     * @param array $data
     */
    public function __construct
    (
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \ForMage\Base\Model\Package $package,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->objectFactory = $objectFactory;
        $this->package = $package;
    }

    public function getPackages()
    {
        $packages = [];
        foreach ($this->package->getPackages() as $package) {

            $obj = $this->objectFactory->create();
            $obj->addData($package);

            $packages[] = $obj;
        }

        return $packages;
    }
}
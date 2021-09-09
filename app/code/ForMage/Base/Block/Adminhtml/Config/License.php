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

class License extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'ForMage_Base::config/license.phtml';
    /**
     * @var Magento\Framework\App\Request\Http
     */
    protected $request = null;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct
    (
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        /**
         * Get request
         *
         * @var Magento\Framework\App\Request\Http $request
         */
        $this->request = $context->getRequest();
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getHost()
    {
        if (null !== $store = $this->request->getParam('store'))
        {
            return parse_url($this->_storeManager->getStore($store)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB), PHP_URL_HOST);
        }

        return $_SERVER['HTTP_HOST'];
    }
}
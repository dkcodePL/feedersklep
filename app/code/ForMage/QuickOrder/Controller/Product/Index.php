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

namespace ForMage\QuickOrder\Controller\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use ForMage\QuickOrder\Helper\Data as Helper;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param Helper $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Helper $helper
    )
    {
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
        $this->_helper = $helper;
    }

    public function execute()
    {
        if (!$this->_helper->isAllowed()) {
            throw new NotFoundException(__('404'));
        }

        try {

            $this->_view->loadLayout();
            $this->_view->getPage()->getConfig()->getTitle()->set(__('Products'));
            $this->_view->renderLayout();

        } catch (\Exception $exception) {
            $resultRedirect = $this->_resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }


}
<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package ForMage\MegaMenu
 */
class Edit extends \ForMage\MegaMenu\Controller\Adminhtml\Menu
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \ForMage\MegaMenu\Model\MenuFactory
     */
    protected $_menuFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Edit constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \ForMage\MegaMenu\Model\MenuFactory $menuFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \ForMage\MegaMenu\Model\MenuFactory $menuFactory,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context, $coreRegistry, $menuFactory);
        $this->_messageManager = $context->getMessageManager();
        $this->_coreRegistry = $coreRegistry;
        $this->_menuFactory = $menuFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_MegaMenu::menu');
    }


    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->_redirect->getRefererUrl());

        try {

            $menu = $this->initMenu(true);

            if (!$menu) {
                return $resultRedirect;
            }


            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();

            $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
            $resultPage->addBreadcrumb(__('Menu'), __('Menu'));
            $resultPage->getConfig()->getTitle()->prepend(__('Menu'));
            return $resultPage;

        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());
        }

        return $resultRedirect;


    }


}
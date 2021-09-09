<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action as BackendAction;

class Edit extends BackendAction
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
     * @var \ForMage\Slider\Model\SlideFactory
     */
    protected $_slideFactory;

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
     * @param \ForMage\Slider\Model\SlideFactory $slideFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \ForMage\Slider\Model\SlideFactory $slideFactory,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
        $this->_coreRegistry = $coreRegistry;
        $this->_slideFactory = $slideFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_Slider::edit');
    }


    public function execute()
    {
        $id = $this->getRequest()->getParam('slide_id');

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->_redirect->getRefererUrl());

        try {
            $slide = $this->_slideFactory->create()->load($id);

            $this->_coreRegistry->register('current_slide', $slide);

            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();

            $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
            $resultPage->addBreadcrumb(__('Slide'), __('Slide'));
            $resultPage->getConfig()->getTitle()->prepend(__('Slide'));
            return $resultPage;

        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());
        }

        return $resultRedirect;


    }


}
<?php
namespace ForMage\WholesaleImport\Controller\Adminhtml\Wholesale\Type2;

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
     * @var \ForMage\WholesaleImport\Model\WholesaleFactory
     */
    protected $_wholesaleFactory;

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
     * @param \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
        $this->_coreRegistry = $coreRegistry;
        $this->_wholesaleFactory = $wholesale;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_WholesaleImport::wholesale');
    }


    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $wholesale = $this->_wholesaleFactory->create()->load($id);

        $this->_coreRegistry->register('current_wholesale', $wholesale);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->_redirect->getRefererUrl());

        try {


            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();

            $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
            $resultPage->addBreadcrumb(__('WholesaleImport'), __('WholesaleImport'));
            $resultPage->getConfig()->getTitle()->prepend(__('WholesaleImport'));
            return $resultPage;

        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());
        }

        return $resultRedirect;


    }


}
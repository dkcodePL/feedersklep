<?php

namespace ForMage\WholesaleImport\Controller\Adminhtml\Wholesale\Type2;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action as BackendAction;


class DeleteFile extends BackendAction
{
    /**
     * @var \ForMage\WholesaleImport\Model\TaskFactory
     */
    protected $_task;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \ForMage\WholesaleImport\Model\WholesaleFactory
     */
    protected $_wholesaleFactory;

    /**
     * Import constructor.
     * @param Context $context
     * @param \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale
     * @param \ForMage\WholesaleImport\Model\TaskFactory $task
     */
    public function __construct(
        Context $context,
        \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale,
        \ForMage\WholesaleImport\Model\TaskFactory $task
    )
    {
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
        $this->_task = $task;
        $this->_wholesaleFactory = $wholesale;
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
        $wholesaleModel = $this->_wholesaleFactory->create()->load($id);

        try {

            $wholesaleModel->setFilename('');
            $wholesaleModel->save();


        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());
        }

        $this->_redirect('*/wholesale/index', ['type' => 'data']);


    }


}
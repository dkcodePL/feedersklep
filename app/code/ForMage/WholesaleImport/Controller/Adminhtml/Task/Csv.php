<?php

namespace ForMage\WholesaleImport\Controller\Adminhtml\Task;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action as BackendAction;
use ForMage\WholesaleImport\Helper\Data as Helper;


class Csv extends BackendAction
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
     * @var \ForMage\WholesaleImport\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Csv constructor.
     * @param Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \ForMage\WholesaleImport\Model\TaskFactory $task
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \ForMage\WholesaleImport\Model\TaskFactory $task,
        Helper $helper
    )
    {
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
        $this->_fileFactory = $fileFactory;
        $this->_task = $task;
        $this->_helper = $helper;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_WholesaleImport::task');
    }


    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $taskModel = $this->_task->create()->load($id);


        try {

            $path = BP . '/var/import.csv';

            $products = json_decode($taskModel->getProducts(), true);

            $products = $this->_helper->correctVisibility($products);

            array_unshift($products, array_keys(reset($products)));
            $this->_helper->saveCsv($path, $products);


            $content = file_get_contents($path);
            unlink($path);


            return $this->_fileFactory->create(__('Task') . ': ' . $taskModel->getId() . '.csv', $content, DirectoryList::VAR_DIR, 'text/csv');


        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());

            $this->_redirect('*/task/index');
        }



    }


}
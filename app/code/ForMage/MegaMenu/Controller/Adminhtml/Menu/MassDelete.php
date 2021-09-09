<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use ForMage\MegaMenu\Model\ResourceModel\Menu\CollectionFactory;


class MassDelete extends Action
{
    /**
     * MassActions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * MassStatus constructor.
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_messageManager = $context->getMessageManager();
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');

        $collection = $this->filter->getCollection($this->collectionFactory->create());

        try {

            $deleted = 0;
            /** @var \WMS\FormWidget\Model\ResourceModel\Form $item */
            foreach ($collection->getItems() as $item) {
                $item->delete();
                $deleted++;
            }

            $this->_messageManager->addSuccess(
                __('A total of %1 record(s) have been deleted.', $deleted)
            );


        } catch (\Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }

        return $resultRedirect;

    }
}

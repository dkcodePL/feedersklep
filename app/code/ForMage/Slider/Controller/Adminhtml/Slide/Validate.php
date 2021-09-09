<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Framework\Message\Error;

class Validate extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var array
     */
    protected $_messages = [];

    /**
     * @var bool
     */
    protected $_error = false;

    /**
     * Validate constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_Slider::edit');
    }

    /**
     * Save item.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        try {
            $response->setError(0);

        } catch (\Exception $exception) {
            $response->setError(1);
            $this->_messages[] = $exception->getMessage();
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }

}

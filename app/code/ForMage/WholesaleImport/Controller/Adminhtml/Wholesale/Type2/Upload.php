<?php

namespace ForMage\WholesaleImport\Controller\Adminhtml\Wholesale\Type2;

use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Backend\App\Action
{

    /**
     * @var \ForMage\WholesaleImport\Model\WholesaleFactory
     */
    protected $_wholesale;

    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale
    ) {
        parent::__construct($context);
        $this->_wholesale = $wholesale;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_WholesaleImport::wholesale');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $paramId = $this->_request->getParam('param_name', 'csv');

        try {
            $result = $this->_wholesale->create()->getImageUploader()->saveFileToTmpDir($paramId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
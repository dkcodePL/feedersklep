<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Controller\Adminhtml\Slide;

use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Backend\App\Action
{

    /**
     * @var \ForMage\Slider\Model\SlideFactory
     */
    protected $_slide;

    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \ForMage\Slider\Model\SlideFactory $slide
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \ForMage\Slider\Model\SlideFactory $slide
    ) {
        parent::__construct($context);
        $this->_slide = $slide;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_Slider::edit');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'image');

        try {
            $result = $this->_slide->create()->getImageUploader()->saveFileToTmpDir($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Save
 * @package ForMage\Brand\Controller\Adminhtml\Brand
 */
class Save extends \Magento\Backend\App\Action
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
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

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
     * @var array '
     */
    protected $_images = ['image'];

    /**
     * Save constructor.
     * @param Context $context
     * @param \ForMage\Slider\Model\SlideFactory $slide
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \ForMage\Slider\Model\SlideFactory $slide,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
        $this->_slideFactory = $slide;
        $this->_coreRegistry = $coreRegistry;
        $this->resultJsonFactory = $resultJsonFactory;
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
        $post = $this->getRequest()->getPost();
        foreach ($this->_images as $image) {
            $post = $this->_filterPostData($post, $image);
        }

        try {

            $slide = $this->_slideFactory->create();
            foreach ($this->_images as $image) {
                if (isset($post[$image]) && $post[$image]) {
                    $slide->getImageUploader()->moveFileFromTmp($post[$image], true);
                }
            }

            $slide->addData($post);
            $slide->save();

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/edit', ['slide_id' => $slide->getId()]);

        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
        return $resultRedirect;
    }

    /**
     * @param array $rawData
     * @param string $image
     * @return array
     */
    protected function _filterPostData($rawData, $image)
    {
        $data = (array)$rawData;
        if (isset($data[$image]) && is_array($data[$image])) {
            if (!empty($data[$image]['delete'])) {
                $data[$image] = null;
            } else {
                if (isset($data[$image][0]['name']) && isset($data[$image][0]['tmp_name'])) {
                    $data[$image] = $data[$image][0]['name'];
                } else {
                    unset($data[$image]);
                }
            }
        }
        return $data;
    }


}
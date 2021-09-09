<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Controller\Adminhtml\Menu;

class RefreshPath extends \ForMage\MegaMenu\Controller\Adminhtml\Menu
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

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
     * RefreshPath constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \ForMage\MegaMenu\Model\MenuFactory $menuFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \ForMage\MegaMenu\Model\MenuFactory $menuFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context, $coreRegistry, $menuFactory);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Build response for refresh input element 'path' in form
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $categoryId = (int)$this->getRequest()->getParam('id');
        if ($categoryId) {
            $category = $this->_objectManager->create(\ForMage\MegaMenu\Model\Menu::class)->load($categoryId);

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData([
                'id' => $categoryId,
                'path' => $category->getPath(),
                'parentId' => $category->getParentId(),
            ]);
        }
    }
}

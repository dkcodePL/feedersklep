<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\Category as CategoryModel;
use ForMage\MegaMenu\Helper\Data as Helper;

/**
 * Class Save
 * @package ForMage\MegaMenu
 */
class Save extends \ForMage\MegaMenu\Controller\Adminhtml\Menu
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
     * @var Helper
     */
    protected $_helper;

    /**
     * Save constructor.
     * @param Context $context
     * @param \ForMage\MegaMenu\Model\MenuFactory $menuFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        \ForMage\MegaMenu\Model\MenuFactory $menuFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        Helper $helper
    )
    {
        parent::__construct($context, $coreRegistry, $menuFactory);
        $this->_messageManager = $context->getMessageManager();
        $this->_menuFactory = $menuFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_MegaMenu::menu');
    }


    public function execute()
    {
        $post = $this->getRequest()->getPost();

        try {

            $menu = $this->initMenu();

            if ($menu->getId() && $menu->getParent() > CategoryModel::TREE_ROOT_ID) {
                unset($post['store_ids']);
            }

            if ($post['type'] == 'category') {
                $post['type_id'] = $post['categories'];
            }

            $menu->addData((array)$post);

            if (!$menu->getId()) {
                $this->newMenu($menu);
            }

            $menu->save();

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/edit', ['id' => $menu->getId()]);


        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
        return $resultRedirect;
    }

    /**
     * @param \ForMage\MegaMenu\Model\Menu $menu
     */
    protected function newMenu(\ForMage\MegaMenu\Model\Menu $menu)
    {
        $parentId = $this->getRequest()->getParam('parent');
        if (!$parentId) {
            $parentId = CategoryModel::TREE_ROOT_ID;
        }
        $parentCategory = $this->_menuFactory->create()->load($parentId);

        if ($parentId > CategoryModel::TREE_ROOT_ID) {
            $menu->setStoreIds($parentCategory->getStoreIds());
        }
        $menu->setPath($parentCategory->getPath());
        $menu->setParentId($parentId);
    }



}
<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use ForMage\MegaMenu\Controller\Adminhtml\Menu;
use ForMage\MegaMenu\Model\MenuFactory;

/**
 * Class CategoriesJson
 * @package ForMage\MegaMenu
 */
class CategoriesJson extends Menu
{
    /**
     * JSON Result Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Layout Factory
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * CategoriesJson constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param MenuFactory $menuFactory
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        MenuFactory $menuFactory,
        JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;

        parent::__construct($context, $coreRegistry, $menuFactory);
    }

    /**
     * Get tree node (Ajax version)
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->setIsTreeWasExpanded(
            (boolean)$this->getRequest()->getParam('expand_all')
        );

        $resultJson = $this->resultJsonFactory->create();
        if ($id = (int)$this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $id);

            $menu = $this->initMenu(true);
            if (!$menu) {
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('megamenu/*/', ['_current' => true]);
            }

            $treeJson = $this->layoutFactory->create()
                ->createBlock('ForMage\MegaMenu\Block\Adminhtml\Menu\Tree')
                ->getTreeJson($menu);

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            return $resultJson->setJsonData($treeJson);
        }

        return $resultJson->setJsonData('[]');
    }
}

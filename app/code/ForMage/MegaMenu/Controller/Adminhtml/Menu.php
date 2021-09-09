<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use ForMage\MegaMenu\Model\MenuFactory;

/**
 * Class Menu
 * @package ForMage\MegaMenu
 */
abstract class Menu extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'ForMage_MegaMenu::menu';

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Menu constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param MenuFactory $menuFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        MenuFactory $menuFactory
    )
    {
        parent::__construct($context);
        $this->menuFactory = $menuFactory;
        $this->coreRegistry = $coreRegistry;


    }

    /**
     * @param bool $register
     * @return bool|\ForMage\Brand\Model\Brand
     */
    public function initMenu($register = false)
    {
        $id = null;
        if ($this->getRequest()->getParam('id')) {
            $id = (int)$this->getRequest()->getParam('id');
        } elseif ($this->getRequest()->getParam('category_id')) {
            $id = (int)$this->getRequest()->getParam('category_id');
        }

        $menu = $this->menuFactory->create();
        if ($id) {
            $menu->load($id);
            if (!$menu->getId()) {
                $this->messageManager->addErrorMessage(__('This menu no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('category', $menu);
        }

        return $menu;
    }
}

<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Delete extends \ForMage\MegaMenu\Controller\Adminhtml\Menu
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
     * Delete constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \ForMage\MegaMenu\Model\MenuFactory $menuFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \ForMage\MegaMenu\Model\MenuFactory $menuFactory
    )
    {
        parent::__construct($context, $coreRegistry, $menuFactory);
        $this->_messageManager = $context->getMessageManager();
        $this->_coreRegistry = $coreRegistry;
        $this->_menuFactory = $menuFactory;
    }

    public function execute()
    {

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');

        try {

            $menu = $this->initMenu();

            if (!$menu) {
                return $resultRedirect;
            }

            $menu->delete();

            return $resultRedirect;

        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());
        }

        return $resultRedirect;


    }


}
<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;

class Move extends \ForMage\MegaMenu\Controller\Adminhtml\Menu
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    protected $logger;

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
     * Move constructor.
     * @param Context $context
     * @param \ForMage\MegaMenu\Model\MenuFactory $menuFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \ForMage\MegaMenu\Model\MenuFactory $menuFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        parent::__construct($context, $coreRegistry, $menuFactory);
        $this->_messageManager = $context->getMessageManager();
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->logger = $logger;
    }

    /**
     * Move Menu action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        /**
         * New parent menu identifier
         */
        $parentNodeId = $this->getRequest()->getPost('pid', false);
        /**
         * Menu id after which we have put our category
         */
        $prevNodeId = $this->getRequest()->getPost('aid', false);

        /** @var $block \Magento\Framework\View\Element\Messages */
        $block = $this->layoutFactory->create()->getMessagesBlock();
        $error = false;

        try {

            $menu = $this->initMenu();
            if ($menu === false) {
                throw new \Exception(__('Menu is not available for requested store.'));
            }

            if ($parentNodeId !== $menu->getParentId()) {
                throw new \Exception(__(':)'));
            }

            $menu->move($parentNodeId, $prevNodeId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->_messageManager->addExceptionMessage($e);
        } catch (\Exception $e) {
            $error = true;
            $this->_messageManager->addErrorMessage(__('There was a menu move error.'));
            $this->_messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical($e);
        }

        if (!$error) {
            $this->_messageManager->addSuccessMessage(__('You moved the menu.'));
        }

        $block->setMessages($this->_messageManager->getMessages(true));
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'messages' => $block->getGroupedHtml(),
            'error' => $error
        ]);
    }
}
<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\Message\Error;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Store\Model\Store;

class Validate extends Action
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
     * @param \ForMage\MegaMenu\Model\MenuFactory $menuFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \ForMage\MegaMenu\Model\MenuFactory $menuFactory
    )
    {
        parent::__construct($context);
        $this->_menuFactory = $menuFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_MegaMenu::menu');
    }

    /**
     * Save item.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {

            $this->_validate();

        } catch (\Exception $exception) {
            $this->_messages[] = $exception->getMessage();
        }

        $response = new \Magento\Framework\DataObject();
        //$response->setError(1);
        $response->setError($this->_error);
        $response->setMessages($this->_messages);

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }

    protected function _validate()
    {

        $menu = $this->_menuFactory->create();
        $post = $this->getRequest()->getPost();

        $parentId = isset($post['parent']) && $post['parent'] ? $post['parent'] : CategoryModel::TREE_ROOT_ID;
        $parent = $menu->load($parentId);



        $post['store_ids'];

        if (!in_array(Store::DEFAULT_STORE_ID, $parent->getStoreIds()) ||  in_array(Store::DEFAULT_STORE_ID, $parent->getStoreIds())) {

              //  $this->_messages = array_diff([1,2, 4], [1,3]);


        }




    }


}

<?php


namespace ForMage\FeederTheme\Controller\Cenowki;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;


class Index extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        try {




            $this->_view->loadLayout();
            $this->_view->getPage()->getConfig()->getTitle()->set(__(''));
            $this->_view->renderLayout();


        } catch (Exception $e) {


        }


    }


}
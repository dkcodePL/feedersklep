<?php

namespace Smartmage\Paczkomaty2Light\Controller\Index;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Smartmage\Paczkomaty2Light\Helper\Data;

/**
 * Class Index
 * @package Smartmage\Paczkomaty2Light\Controller\Index
 */
class Index extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;
    /**
     * @var \Smartmage\Paczkomaty2Light\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Magento\Checkout\Model\Session                    $session
     * @param \Magento\Framework\App\Request\Http                $request
     * @param \Smartmage\Paczkomaty2Light\Helper\Data            $_helper
     * @param \Magento\Framework\Controller\Result\JsonFactory   $_resultJsonFactory
     * @param \Psr\Log\LoggerInterface                           $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Session $session,
        Http $request,
        Data $_helper,
        JsonFactory $_resultJsonFactory,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_session           = $session;
        $this->_request           = $request;
        $this->_helper            = $_helper;
        $this->_resultJsonFactory = $_resultJsonFactory;
        $this->_logger            = $logger;
        $this->_scopeConfig       = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $quote                = $this->_session->getQuote();
        $payment              = $this->_request->getParam('payment');
        $machine              = $this->_request->getParam('machine');
        $machineType          = $this->_request->getParam('type');
        $pointData            = false;
        $isPOP                = false;
        $apiConnectionProblem = false;
        if (is_null($machineType) && $payment == 'cod')
        {
            $pointData = $this->_helper->getPointData($machine);
            if (!$pointData) {
                $apiConnectionProblem = true;
            } else {
                $machineType = $pointData->type;
            }
        }

        if (is_array($machineType) && in_array('pop',$machineType))
        {
            $isPOP = true;
        }
        $pointData = false;

        // w trybie testowym nie występują niektóre paczkomaty i POPy (a niestety
        // widget paczkomatów tego nie uwzględnia) więc sprawdzam za każdym razem
        if ($this->_helper->getIsTestMode()) {
            $pointData = $this->_helper->getPointData($machine);
            if ($pointData && $pointData->status == 404) {
                return $this->_resultJsonFactory->create()->setData([
                    'status' => 0,
                    'message' => __('Niestety wybrany punkt nie został znaleziony. Wybierz inny punkt.')
                ]);
            } elseif (!$pointData) {
                $apiConnectionProblem = true;
            }
        }

        if ($apiConnectionProblem) {
            // funkcjonalność przeniesiona z pełnej wersji modułu, ale bez konfiguracji
            // if ($this->_scopeConfig->getValue(
            // 'shipping/smpaczkomaty2/allow_on_error',
            // \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->_logger->error(print_r($pointData, true));
            return $this->_resultJsonFactory->create()->setData([
                'status' => 1,
                'is_pop' => $isPOP,
            ]);
            // } else {
            // $this->_logger->error(print_r($pointData, true));
            // return $this->_resultJsonFactory->create()->setData(array(
            // 'status' => 0,
            // 'message' => __('Wystąpił problem z połączeniem do API Inpostu. Spróbuj ponownie za chwilę lub wybierz inną metodę dostawy.')
            // ));
            // }
        }

        $quote->setData('paczkomaty_target_point', $machine)->save();
        $this->_session->setPaczkomatyTargetPoint($machine);
        $this->_session->setPaczkomatyPOP($isPOP);
        return $this->_resultJsonFactory->create()->setData(['status' => 1,'is_pop' => $isPOP]);
    }
}
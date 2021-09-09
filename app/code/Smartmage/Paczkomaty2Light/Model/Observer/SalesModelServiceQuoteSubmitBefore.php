<?php

namespace Smartmage\Paczkomaty2Light\Model\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use Smartmage\Paczkomaty2Light\Helper\Data;

/**
 * Class SalesModelServiceQuoteSubmitBefore
 * @package Smartmage\Paczkomaty2Light\Model\Observer
 */
class SalesModelServiceQuoteSubmitBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Smartmage\Paczkomaty2Light\Helper\Data
     */
    protected $paczkomatyHelper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;


    /**
     * SalesModelServiceQuoteSubmitBefore constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(
        Data $paczkomatyData,
        LoggerInterface $logger,
        Session $session,
        ObjectManagerInterface $objectmanager
    ) {
        $this->_objectManager   = $objectmanager;
        $this->paczkomatyHelper = $paczkomatyData;
        $this->session          = $session;
        $this->logger           = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();

        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $comment = __('Brak wybranego paczkomatu');
        if ($selectedPaczkomat = $quote->getData('paczkomaty_target_point')) {
            $comment = __('Wybrany paczkomat: %1', $selectedPaczkomat);
        } elseif ($selectedPaczkomat = $this->session->getPaczkomatyTargetPoint()) {
            $comment = __('Wybrany paczkomat %1', $selectedPaczkomat);
        }



        $shippingMethod = $order->getShippingMethod();
        if ($shippingMethod == 'smpaczkomaty2_paczkomaty2' || $shippingMethod == 'smpaczkomaty2_paczkomaty2cod') {
            $order->setPaczkomat($selectedPaczkomat);
            $order->addStatusHistoryComment($comment)->setIsCustomerNotified(false);
            $order->save();
        }
    }

}
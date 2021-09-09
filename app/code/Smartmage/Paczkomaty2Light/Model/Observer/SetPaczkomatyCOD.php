<?php

namespace Smartmage\Paczkomaty2Light\Model\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetPaczkomatyCOD
 * @package Smartmage\Paczkomaty2Light\Model\Observer
 */
class SetPaczkomatyCOD implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $_scopeConfig;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;

    /**
     * SetPaczkomatyCOD constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session                    $checkoutSession
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_scopeConfig     = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if ($this->_scopeConfig->getValue('carriers/smpaczkomaty2/disable_cash_on_delivery')) {
            $event       = $observer->getEvent();
            $method      = $event->getMethodInstance();
            $result      = $event->getResult();
            $paymentCode = $method->getCode();

            if ($codPayments = $this->_scopeConfig->getValue('carriers/smpaczkomaty2/cod_methods')) {
                $codPayments = explode(',', $codPayments);
            } else {
                $codPayments = array();
            }

            $carrierName = $this->_checkoutSession->getQuote()->getShippingAddress()->getShippingMethod();
            if (strpos($carrierName, 'smpaczkomaty2') !== false) {
                if (in_array($paymentCode, $codPayments)) {
                    $result->setData('is_available', false);
                }
            }
        }

        return $this;
    }


}
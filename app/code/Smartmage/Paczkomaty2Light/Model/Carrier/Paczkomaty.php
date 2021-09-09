<?php

namespace Smartmage\Paczkomaty2Light\Model\Carrier;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Paczkomaty
 * @package Smartmage\Paczkomaty2Light\Model\Carrier
 */
class Paczkomaty extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'smpaczkomaty2';


    /**
     * Paczkomaty constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Checkout\Model\Session                             $checkoutSession
     * @param array                                                       $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Session $checkoutSession,
        array $data = []
    ) {
        $this->checkoutSession    = $checkoutSession;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [
            'paczkomaty2' => $this->getConfigData('name')
            ,
            'paczkomaty2cod' => $this->getConfigData('name_cod')
        ];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        $cartItems = $request->getAllItems();

        if (!$this->getConfigFlag('active')) {
            return $this->getErrorMessage();
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        foreach ($cartItems as $cartItem) {
            if ($cartItem->getProduct()->getData('disable_paczkomaty_shipping') == true) {
                return $result;
            }
        }

        $discount = 0;
        if ($request->getPackageValue() != $request->getPackageValueWithDiscount()) {
            $discount = $request->getPackageValue() - $request->getPackageValueWithDiscount();
        }

        $total_price = $request->getBaseSubtotalInclTax() - $discount;

        $packageValue = $request->getBaseCurrency()->convert($total_price, $request->getPackageCurrency());

        $free_shipping_from = $this->getConfigData('free_shipping_prepayment');

        // Przedpłata
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();
        $method->setMethod('paczkomaty2');
        $method->setMethodTitle($this->getConfigData('title'));
        $method->setCarrier('smpaczkomaty2');
        $method->setCarrierTitle($this->getConfigData('name'));
        if ($free_shipping_from > 0 && $packageValue > $free_shipping_from) {
            $amount = 0;
        } else {
            $amount = $this->getConfigData('price');
        }

        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);


        // Dostawa za pobraniem w paczkomacie
        if ($this->getConfigData('cod_active')) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $cod_method = $this->_rateMethodFactory->create();
            $cod_method->setMethod('paczkomaty2cod');
            $cod_method->setMethodTitle($this->getConfigData('title'));
            $cod_method->setCarrier('smpaczkomaty2');
            $cod_method->setCarrierTitle($this->getConfigData('name_cod'));

            $free_shipping_from = $this->getConfigData('free_shipping_cod');
            if ($free_shipping_from > 0 && $packageValue > $free_shipping_from) {
                $amount = 0;
            } else {
                $amount = $this->getConfigData('price_cod');
            }

            $cod_method->setPrice($amount);
            $cod_method->setCost($amount);
            $result->append($cod_method);
        }

        return $result;
    }
}
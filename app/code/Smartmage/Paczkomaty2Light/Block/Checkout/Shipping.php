<?php

namespace Smartmage\Paczkomaty2Light\Block\Checkout;

use Magento\Checkout\Block\Onepage;
use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Shipping
 * @package Smartmage\Paczkomaty2Light\Block\Checkout
 */
class Shipping extends Onepage
{

    /**
     * @var \Magento\Quote\Model\Quote
     */
    public $quote;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Form\FormKey             $formKey
     * @param \Magento\Checkout\Model\CompositeConfigProvider  $configProvider
     * @param array                                            $layoutProcessors
     * @param array                                            $data
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        CompositeConfigProvider $configProvider,
        array $layoutProcessors = [],
        array $data = [],
        Session $checkoutSession
    ) {
        parent::__construct($context, $formKey, $configProvider, $layoutProcessors, $data);
        $this->quote            = $checkoutSession->getQuote();
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @return bool|mixed
     */
    public function getPaczkomatyTargetPoint()
    {
        if ($this->quote->getData('paczkomaty_target_point')) {
            return $this->quote->getData('paczkomaty_target_point');
        } elseif ($this->_checkoutSession->getPaczkomatyTargetPoint()) {
            return $this->_checkoutSession->getPaczkomatyTargetPoint();
        }
        return false;
    }

    public function getIsPOP()
    {
        $paczkomatyPOP = $this->_checkoutSession->getPaczkomatyPOP();
        if ($paczkomatyPOP)
        {
            return $paczkomatyPOP;
        }
        else
        {
            return false;
        }
    }
}

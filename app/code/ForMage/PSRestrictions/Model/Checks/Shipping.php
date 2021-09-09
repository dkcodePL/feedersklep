<?php

namespace ForMage\PSRestrictions\Model\Checks;

use ForMage\PSRestrictions\Helper\Data as Helper;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Model\Quote;

class Shipping implements \Magento\Payment\Model\Checks\SpecificationInterface
{

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Result constructor.
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable(MethodInterface $paymentMethod, Quote $quote)
    {
        $isApplicable = true;

        if (!$this->helper->isEnabled() || $this->helper->isAdmin()) return $isApplicable;

        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();

        $rates = $this->helper->getShippingRestrictions();
        if (!isset($rates[$shippingMethod])) return $isApplicable;

        if (!empty($rates[$shippingMethod]['payment']) && !in_array($paymentMethod->getCode(), $rates[$shippingMethod]['payment'])) {
            $isApplicable = false;
        }

        return $isApplicable;
    }
}
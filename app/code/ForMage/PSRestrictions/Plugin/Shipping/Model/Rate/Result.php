<?php

namespace ForMage\PSRestrictions\Plugin\Shipping\Model\Rate;

use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Shipping\Model\Rate\Result as Subject;
use ForMage\PSRestrictions\Helper\Data as Helper;

/**
 * Class Result
 * @package ForMage\PSRestrictions
 */
class Result
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
     * @param Subject $subject
     * @param Method[] $result
     * @return Method[]
     */
    public function afterGetAllRates(Subject $subject, $result)
    {
        if (!$this->helper->isEnabled() || $this->helper->isAdmin() || !$this->helper->hideShippingMethods()) return $result;

        $data = [];
        $rates = $this->helper->getShippingRestrictions();
        foreach ($result ?: [] as $rate) {

            $code = $rate->getCarrier() . '_' . $rate->getMethod();
            if (!isset($rates[$code])) continue;

            if (!empty($rates[$code]['group']) && !in_array($this->helper->getCurrentCustomerGroupId(), $rates[$code]['group'])) continue;

            $data[] = $rate;
        }


        if ($this->helper->onlyFreeMethods()) {
            $rates = $this->getFreeRates($data);
            return (count($rates) > 0) ? $rates : $data;
        }

        return $data;

    }

    /**
     * @param $rates
     * @return array
     */
    public function getFreeRates($rates)
    {
        $data = [];
        foreach ($rates ?: [] as $rate) {
            if ($rate->getPrice() > 0) continue;
                $data[] = $rate;
        }
        return $data;
    }
}

<?php

namespace ForMage\PSRestrictions\Block\Adminhtml\Form\Field;

class Payment extends \Magento\Framework\View\Element\Html\Select {

    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;

    /**
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Payment\Helper\Data $paymentData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_paymentData = $paymentData;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value) {
        return $this->setName($value . '[]');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml() {
        if (!$this->getOptions()) {

            $payments = $this->_paymentData->getPaymentMethodList();

            foreach ($payments as $paymentId => $paymentLabel) {
                $this->addOption($paymentId, __($paymentLabel));
            }
        }
        return parent::_toHtml();
    }

    public function calcOptionHash($optionValue)
    {
        if (is_array($optionValue)) $optionValue = implode(',', $optionValue);

        return sprintf('%u', crc32($this->getName() . $this->getId() . $optionValue));
    }

}

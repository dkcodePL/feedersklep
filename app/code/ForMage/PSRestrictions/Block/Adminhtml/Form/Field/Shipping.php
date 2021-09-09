<?php

namespace ForMage\PSRestrictions\Block\Adminhtml\Form\Field;

class Shipping extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $_shippingConfig;

    /**
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingConfig,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_scopeConfig = $scopeConfig;
        $this->_shippingConfig = $shippingConfig;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {

            $carriers = $this->_shippingConfig->getAllCarriers();
            foreach ($carriers as $carrierCode => $carrierModel) {

                if (!$carrierModel->isActive()) {
                    continue;
                }
                $carrierMethods = $carrierModel->getAllowedMethods();
                if (!$carrierMethods) {
                    continue;
                }
                $carrierTitle = $this->_scopeConfig->getValue(
                    'carriers/' . $carrierCode . '/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $methods[$carrierCode] = ['label' => $carrierTitle, 'value' => []];

                foreach ($carrierMethods as $methodCode => $methodTitle) {
                    $methods[$carrierCode]['value'][] = [
                        'value' => $carrierCode . '_' . $methodCode,
                        'label' => $methodTitle,
                    ];
                }
                $this->addOption($methods[$carrierCode]['value'], __($carrierTitle));
            }
        }
        return parent::_toHtml();
    }

}

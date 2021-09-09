<?php

namespace ForMage\PSRestrictions\Helper;

use Magento\Catalog\Model\ResourceModel;
use Magento\Framework\App\Http\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const RESTRICTIONS_ENABLED = 'psrestrictions/restrictions/enabled';
    const ONLY_FREE_METHODS = 'psrestrictions/restrictions/free';
    const AVAILABLE_METHODS = 'psrestrictions/restrictions/available';
    const HIDE_SHIPPING_METHODS = 'psrestrictions/restrictions/shipping';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Context
     */
    protected $httpContext;

    protected $_state;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSessionFactory;


    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Context $httpContext
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Context $httpContext,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Registry $registry
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->_state = $state;
        $this->httpContext = $httpContext;
        $this->_customerSessionFactory = $customerSessionFactory;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfigValue($path, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->_scopeConfig->getValue($path, $scope);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->getConfigValue(self::RESTRICTIONS_ENABLED);
    }

    /**
     * @return bool
     */
    public function hideShippingMethods()
    {
        return (bool)$this->getConfigValue(self::HIDE_SHIPPING_METHODS);
    }

    public function isAdmin()
    {
        return (bool)\Magento\Framework\App\Area::AREA_ADMINHTML === $this->_state->getAreaCode();
    }

    /**
     * @return bool
     */
    public function onlyFreeMethods()
    {
        return (bool)$this->getConfigValue(self::ONLY_FREE_METHODS);
    }

    /**
     * @return array
     */
    public function getShippingRestrictions()
    {
        $methods = [];
        $data = $this->getConfigValue(
            self::AVAILABLE_METHODS
        );
        $data = json_decode($data, true);
        if ($data && is_array($data)) {
            foreach ($data as $row) {
                $methods[$row['shipping_method']]['payment'] = isset($row['payment_method']) ? $row['payment_method'] : [];
                $methods[$row['shipping_method']]['group'] = isset($row['customer_group']) ? $row['customer_group'] : [];
            }
        }
        return $methods;
    }

    public function getCurrentCustomerGroupId()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
    }


}

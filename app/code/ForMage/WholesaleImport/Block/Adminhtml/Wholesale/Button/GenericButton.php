<?php

namespace ForMage\WholesaleImport\Block\Adminhtml\Wholesale\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * GenericButton constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->context = $context;
        $this->_coreRegistry = $coreRegistry;
    }

    public function getItem($code)
    {
        return $this->_coreRegistry->registry($code);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    public function getItemId($id = 'id')
    {
        return $this->context->getRequest()->getParam($id);

    }
}

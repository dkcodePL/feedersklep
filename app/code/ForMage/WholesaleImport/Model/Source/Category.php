<?php

namespace ForMage\WholesaleImport\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Category implements OptionSourceInterface
{

    /**
     * @var \ForMage\WholesaleImport\Model\WholesaleFactory
     */
    protected $_wholesaleFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Category constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_wholesaleFactory = $wholesale;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];

        $wholesale = $this->_coreRegistry->registry('current_wholesale');

        $wholesaleModel = $this->_objectManager->create($wholesale->getWholesaleModel());

        foreach ($wholesaleModel->getCategories() as $value => $label) {
            $data[] = [
                'label' => $label,
                'value' => $value
            ];
        }
        return $data;
    }
}

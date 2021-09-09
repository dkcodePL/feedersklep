<?php

namespace ForMage\WholesaleImport\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CsvField implements OptionSourceInterface
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
     * XmlAttribute constructor.
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
        $filePath = $wholesaleModel->getFilePath($wholesale->getFilename());

        if (!$wholesaleModel->ifFileExists($wholesale->getFilename())) return $data;

        foreach ($wholesaleModel->getFieldsFromCsv($filePath) as $item ) {
            $data[] = [
                'label' => $item,
                'value' => $item
            ];
        }
        return $data;
    }
}

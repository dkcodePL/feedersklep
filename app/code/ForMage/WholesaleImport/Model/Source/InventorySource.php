<?php

namespace ForMage\WholesaleImport\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class InventorySource implements OptionSourceInterface
{

    protected $_sourceRepository;

    public function __construct(
        \Magento\Inventory\Model\ResourceModel\Source\Collection $sourceRepository
    )
    {
        $this->_sourceRepository = $sourceRepository;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];
        $sources = $this->_sourceRepository;

        foreach ($sources as $source) {
            $data[] = [
                'label' => $source->getName(),
                'value' => $source->getSourceCode(),
            ];
        }

        return $data;
    }
}

<?php

namespace ForMage\FeederTheme\Model\Category\Attribute\Source;

use ForMage\FeederTheme\Helper\Data as Helper;

class Manufacturer extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * Manufacturer constructor.
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    )
    {
        $this->_helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {

            $manufacturers = $this->_helper->getProductAttributeOptions('manufacturer');
            foreach ($manufacturers as $value => $label) {
                $this->_options[] = [
                    'label' => $label,
                    'value' => $value,
                ];
            }
        }
        return $this->_options;
    }



}

<?php

namespace ForMage\PSRestrictions\Block\Adminhtml\Form\Field;

/**
 * HTML select element block with customer groups options
 */
class Group extends \Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup
{
    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addGroupAllOption = false;

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value) {
        return $this->setName($value . '[]');
    }

    public function calcOptionHash($optionValue)
    {
        if (is_array($optionValue)) $optionValue = implode(',', $optionValue);

        return sprintf('%u', crc32($this->getName() . $this->getId() . $optionValue));
    }
}

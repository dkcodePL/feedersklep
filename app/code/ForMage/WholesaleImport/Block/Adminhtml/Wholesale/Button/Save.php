<?php

namespace ForMage\WholesaleImport\Block\Adminhtml\Wholesale\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;


/**
 * Class Save
 */
class Save extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'on_click' => '',
            'sort_order' => 20
        ];
    }
}

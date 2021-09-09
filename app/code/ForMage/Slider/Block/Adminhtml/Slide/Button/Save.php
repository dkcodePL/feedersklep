<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Block\Adminhtml\Slide\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;


class Save implements ButtonProviderInterface
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

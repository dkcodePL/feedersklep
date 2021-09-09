<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Block\Adminhtml\Form\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;


/**
 * Class Import
 */
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
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'menu.menu',
                                'actionName' => 'save'
                            ]
                        ]
                    ]
                ],

            ],
            'on_click' => '',
            'sort_order' => 20
        ];
    }
}

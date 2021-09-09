<?php

namespace ForMage\WholesaleImport\Block\Adminhtml\Wholesale\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;


/**
 * Class Import
 */
class Import extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $wholesale = $this->getItem('current_wholesale');

        if (!$wholesale->getId() || !$wholesale->getFilename()) return [];

        return [
            'label' => __('Import'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('wholesaleimport/wholesale_type2/import', ['id' => $wholesale->getId()])),
            'class' => '',
            'sort_order' => 10
        ];
    }

}

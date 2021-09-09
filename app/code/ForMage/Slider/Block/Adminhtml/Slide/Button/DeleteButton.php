<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Block\Adminhtml\Slide\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class DeleteButton extends \Magento\Cms\Block\Adminhtml\Block\Edit\GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getItemId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * URL to send delete requests to.
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['slide_id' => $this->getItemId()]);
    }

    public function getItemId()
    {
        return $this->context->getRequest()->getParam('slide_id');

    }
}

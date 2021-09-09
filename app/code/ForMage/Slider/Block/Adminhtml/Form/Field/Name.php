<?php
/**
 * @copyright Copyright (c) 2018 - 2020 adam@intw.pl
 */

namespace ForMage\Slider\Block\Adminhtml\Form\Field;

class Name extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * @var ForMage\Slider\Block\Adminhtml\Form\Field\Group
     */
    protected $_groupRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return ForMage\Slider\Block\Adminhtml\Form\Field\Group
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                'ForMage\Slider\Block\Adminhtml\Form\Field\Group',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_groupRenderer;
    }



    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'group_id', ['label' => __('Group'), 'renderer' => $this->_getGroupRenderer()]
        );
        $this->addColumn(
            'name', ['label' => __('Name'), 'class' => 'required-entry']
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('group_id'))] = 'selected="selected"';
        $row->setData(
            'option_extra_attrs', $optionExtraAttr
        );
    }

}

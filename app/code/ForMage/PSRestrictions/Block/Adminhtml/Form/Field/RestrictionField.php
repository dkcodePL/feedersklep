<?php

namespace ForMage\PSRestrictions\Block\Adminhtml\Form\Field;

class RestrictionField extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {

    /**
     * @var Shipping Methods
     */
    protected $_shippingRenderer;

    /**
     * @var Payment Methods
     */
    protected $_paymentRenderer;

    /**
     * @var Customergroup
     */
    protected $_groupRenderer;

    /**
     * Retrieve shipping column renderer
     *
     * @return Shipping Methods
     */
    protected function _getShippingRenderer() {
        if (!$this->_shippingRenderer) {
            $this->_shippingRenderer = $this->getLayout()->createBlock(
                'ForMage\PSRestrictions\Block\Adminhtml\Form\Field\Shipping', '', ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_shippingRenderer->setClass('required-entry');
        }
        return $this->_shippingRenderer;
    }

    /**
     * Retrieve payment column renderer
     *
     * @return Payment Methods
     */
    protected function _getPaymentRenderer() {
        if (!$this->_paymentRenderer) {
            $this->_paymentRenderer = $this->getLayout()->createBlock(
                'ForMage\PSRestrictions\Block\Adminhtml\Form\Field\Payment', '', ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_paymentRenderer->setExtraParams('multiple');
        }
        return $this->_paymentRenderer;
    }

    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                'ForMage\PSRestrictions\Block\Adminhtml\Form\Field\Group',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_shippingRenderer->setClass('required-entry');
            $this->_groupRenderer->setExtraParams('multiple');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender() {
        $this->addColumn(
            'shipping_method', ['label' => __('Shipping Method'), 'renderer' => $this->_getShippingRenderer()]
        );
        $this->addColumn(
            'customer_group', ['label' => __('Customer Group'), 'renderer' => $this->_getGroupRenderer()]
        );
        $this->addColumn(
            'payment_method', ['label' => __('Payment Method'), 'renderer' => $this->_getPaymentRenderer()]
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
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {

        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getShippingRenderer()->calcOptionHash($row->getData('shipping_method'))] = 'selected="selected"';

        foreach ($row->getData('payment_method') ?: [] as $item) {
            $optionExtraAttr['option_' . $this->_getPaymentRenderer()->calcOptionHash($item)] = 'selected="selected"';
        }

        foreach ($row->getData('customer_group') ?: [] as $item) {
            $optionExtraAttr['option_' . $this->_getGroupRenderer()->calcOptionHash($item)] = 'selected="selected"';
        }

        $row->setData(
            'option_extra_attrs', $optionExtraAttr
        );
    }

}

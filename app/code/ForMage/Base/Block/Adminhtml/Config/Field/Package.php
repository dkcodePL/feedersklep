<?php
/**
 * 4mage.co Package
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the 4mage.co license that is
 * available through the world-wide-web at this URL:
 * https://4mage.co/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	ForMage
 * @package 	ForMage_Base
 * @copyright 	Copyright (c) 2020 4mage.co (https://4mage.co/)
 * @license  	https://4mage.co/license-agreement/
 */


namespace ForMage\Base\Block\Adminhtml\Config\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Package extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Get element content
     *
     * @see \Magento\Config\Block\System\Config\Form\Field::_getElementHtml()
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = parent::_getElementHtml($element);

        return $html . $this->getLayout()->createBlock('ForMage\Base\Block\Adminhtml\Config\Package')->toHtml();
    }
}
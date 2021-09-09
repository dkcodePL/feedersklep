<?php
/**
 * @copyright Copyright (c) 2018 - 2020 adam@intw.pl
 */

namespace ForMage\Slider\Block\Adminhtml\Form\Field;

class Group extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \ForMage\Slider\Model\Config\Source\Group
     */
    protected $_group;

    /**
     * Group constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \ForMage\Slider\Model\Config\Source\Group $group
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \ForMage\Slider\Model\Config\Source\Group $group,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_group = $group;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {

            $groups = $this->_group->toArray();
            foreach ($groups as $id =>  $item) {
                $this->addOption($id, $item);
            }
        }
        return parent::_toHtml();
    }

}

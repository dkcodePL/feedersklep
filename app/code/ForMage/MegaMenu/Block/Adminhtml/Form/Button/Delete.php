<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Block\Adminhtml\Form\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\Registry;

/**
 * Class DeleteButton
 */
class Delete extends \Magento\Backend\Block\Template implements ButtonProviderInterface
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * DeleteButton constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    /**
     * Delete button
     *
     * @return array
     */
    public function getButtonData()
    {
        $category = $this->registry->registry('category');
        $categoryId = (int)$category->getId();

        if ($categoryId) {
            return [
                'id' => 'delete',
                'label' => __('Delete'),
                'on_click' => sprintf("location.href = '%s';", $this->getDeleteUrl(['id' => $categoryId])),
                'class' => 'delete',
                'sort_order' => 10
            ];
        }

        return [];
    }

    /**
     * @param array $args
     * @return string
     */
    public function getDeleteUrl(array $args = [])
    {
        return $this->getUrl('*/*/delete', $args);
    }
}

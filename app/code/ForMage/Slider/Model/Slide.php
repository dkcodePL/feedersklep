<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Model;

use Magento\Framework\Model\AbstractModel;

class Slide extends AbstractModel {

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
        $this->_init('ForMage\Slider\Model\ResourceModel\Slide');
    }

    /**
     * Slide constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Slide $resource
     * @param \ForMage\Base\Model\ImageUploader $imageUploader
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\Slide $resource,
        \ForMage\Base\Model\ImageUploader $imageUploader,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->imageUploader = $imageUploader;
    }

    /**
     * @return ImageUploader
     */
    public function getImageUploader()
    {
        return $this->imageUploader;
    }

}

<?php

namespace ForMage\WholesaleImport\Model;

use Magento\Framework\Model\AbstractModel;

class Wholesale extends AbstractModel {

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
        $this->_init(ResourceModel\Wholesale::class);
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\Wholesale $resource,
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

    public function getWholeSale()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get($this->getWholesaleModel());
    }

}

<?php

namespace ForMage\WholesaleImport\Cron;

use ForMage\WholesaleImport\Model\Source\{
    Type, Status
};
use ForMage\WholesaleImport\Model\WholesaleFactory;
use ForMage\WholesaleImport\Helper\Data as Helper;
use ForMage\WholesaleImport\Model\ResourceModel\Task\Collection;

class Task
{
    /**
     * @var \ForMage\WholesaleImport\Model\ResourceModel\Task\Collection
     */
    protected $_collection;

    /**
     * @var \ForMage\WholesaleImport\Helper\Data
     */
    protected $_helper;

    /**
     * @var \ForMage\WholesaleImport\Model\WholesaleFactory
     */
    protected $_wholesaleFactory;

    /**
     * Task constructor.
     * @param WholesaleFactory $wholesale
     * @param Collection $collection
     * @param Helper $helper
     */
    public function __construct(
        WholesaleFactory $wholesale,
        Collection $collection,
        Helper $helper
    )
    {
        $this->_collection = $collection;
        $this->_wholesaleFactory = $wholesale;
        $this->_helper = $helper;
    }

    public function tasks()
    {
        $collection = $this->_collection;

        $collection->addFieldToFilter('status', ['in' => [Status::STATUS_PENDING]]);
        $collection->setOrder('id', 'ASC');

        $task = $collection->getFirstItem();

        if (!$task->getId()) return;

        //$wholesale = $this->getWholesale($task->getWholesaleId());


        try {

            switch ($task->getType()) {
                case Type::TYPE_PRODUCTS:
                    $this->importProducts($task);
                    break;
                case Type::TYPE_IMAGES:
                    $this->importImages($task);
                    break;
                case Type::TYPE_DELETE:
                    $this->deleteProducts($task);
                    break;
            }

        } catch (\Exception $exception) {

            $task->setStatus(Status::STATUS_ERROR);
            $task->setMessages($exception->getMessage());
        }
        $task->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        $task->save();

    }

    protected function deleteProducts($task) {

    }

    protected function importImages($task) {

    }

    protected function importProducts($task) {

        $products = json_decode($task->getProducts(), true);


        $this->deleteProductsImages($products);

        $products = $this->_helper->correctVisibility($products);

        $import = $this->_helper->importProducts($products);

        $errors = [];
        if (is_array($import) && !empty($import)) {
            foreach ($import as $item) {
                if (strpos($item, 'Imported resource (image)') !== false) {
                    continue;
                }
                $errors[] = $item;
            }
        }

        if (!empty($errors)) {
            $task->setMessages(implode(', ', $errors));
            $task->setStatus(Status::STATUS_ERROR);
            return false;
        }

        $task->setStatus(Status::STATUS_COMPLETE);
    }

    protected function deleteProductsImages($products)
    {
        foreach ($products as $item) {

            $product = $this->_helper->getProductBySku($item['sku']);
            if (!$product) continue;

            $this->_helper->deleteImagesFromProduct($product);

        }

    }

    protected function getWholesale($id)
    {
        return $this->_wholesaleFactory->create()->load($id);
    }


}

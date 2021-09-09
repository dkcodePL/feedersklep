<?php
declare(strict_types=1);

namespace ForMage\Feeder\Plugin\Order;

use Magento\PageCache\Model\Cache\Type;
use Magento\Inventory\Model\ResourceModel\SourceItem;
use Magento\InventoryIndexer\Indexer\InventoryIndexer;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class  PlaceAfterPlugin
{

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var Type
     */
    private $fullPageCache;

    /**
     * @var InventoryIndexer
     */
    private $inventoryIndexer;

    /**
     * @var SourceItem
     */
    private $sourceItem;


    public function __construct(
        StockRegistryInterface $stockRegistry,
        Type $fullPageCache,
        InventoryIndexer $inventoryIndexer,
        SourceItem $sourceItem
    )
    {
        $this->stockRegistry = $stockRegistry;
        $this->fullPageCache = $fullPageCache;
        $this->inventoryIndexer = $inventoryIndexer;
        $this->sourceItem = $sourceItem;
    }


    /**
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagementInterface
     * @param \Magento\Sales\Model\Order\Interceptor $order
     * @return $order
     */
    public function afterPlace(\Magento\Sales\Api\OrderManagementInterface $orderManagementInterface, $order)
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order_stock.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('ok');


        $orderItems = $order->getAllVisibleItems();
        $tags = [];
        $productSkus = [];
        foreach ($orderItems as $orderItem) {

            try {

                $stockItem = $this->stockRegistry->getStockItemBySku($orderItem->getSku());
                $currentQty = $stockItem->getQty();
                $newQty = $currentQty - $orderItem->getQtyOrdered();


                $logger->info($orderItem->getSku());
                $logger->info($orderItem->getQtyOrdered());
                $logger->info($currentQty);
                $logger->info($newQty);


                $isInStock = (bool)($newQty > 0);
                $stockItem->setQty($newQty);
                $stockItem->setIsInStock($isInStock);

                $productSkus[] = $orderItem->getSku();

                $tags[] = 'CAT_P_' . $orderItem->getProductId();

                $this->stockRegistry->updateStockItemBySku($orderItem->getSku(), $stockItem);

            } catch (\Exception $exception) {

                $logger->info($exception->getMessage());
            }

        }

        try {

            $select = $this->sourceItem->getConnection()->select()->from(
                $this->sourceItem->getMainTable(),
                'source_item_id'
            )->where(
                'sku IN(?)',
                $productSkus
            );

            $sourceItemIds = $this->sourceItem->getConnection()->fetchCol($select);

            if (!empty($sourceItemIds)) {
                $this->inventoryIndexer->executeList($sourceItemIds);
            }

            if (!empty($tags)) {
                $this->fullPageCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
            }


        } catch (\Exception $exception) {
            $logger->info($exception->getMessage());

        }


        return $order;
    }


}
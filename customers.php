<?php

$time_start = microtime(true);

// MAGENTO START
include('app/bootstrap.php');

use Magento\Framework\App\Bootstrap;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;


$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');



$fullPageCache = $objectManager->create('\Magento\PageCache\Model\Cache\Type');
$sourceItem = $objectManager->create('\Magento\Inventory\Model\ResourceModel\SourceItem');
$inventoryIndexer = $objectManager->create('\Magento\InventoryIndexer\Indexer\InventoryIndexer');

$productSkus = [
    '101001616'
];
$tags = [
    'CAT_P_' . 3440
];

$select = $sourceItem->getConnection()->select()->from(
    $sourceItem->getMainTable(),
    'source_item_id'
)->where(
    'sku IN(?)',
    $productSkus
);

$sourceItemIds = $sourceItem->getConnection()->fetchCol($select);

var_export($sourceItemIds);

if (!empty($sourceItemIds)) {
    $inventoryIndexer->executeList($sourceItemIds);
}

if (!empty($tags)) {
    $fullPageCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
}










$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start) / 60;

//execution time of the script
//echo "<pre>";
echo 'Total Execution Time:</b> ' . $execution_time . ' Mins';


<?php

$time_start = microtime(true);

// MAGENTO START
include('app/bootstrap.php');

use Magento\Framework\App\Bootstrap;
use ForMage\WholesaleImport\Model\Source\{
    Type, Status
};


$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

//return $this;


$tax = [
    23 => 10,
    8 => 11,
    5 => 12,
];


$helper = $objectManager->create('ForMage\WholesaleImport\Helper\Data');


$data = [
    [
        'sku' => 'OSM-MLW-EXO',
        'source_code' => 'default',
        'quantity' =>70,
        'status' => 1
    ]
];

$helper->importStocks($data); die();


$file = '/home/feedersklep/public_html/current/var/import/import_piatek.csv';

$csv = $helper->loadCsv($file, ',');

//var_export($csv[0]); die();

$header = $csv[0];



unset($csv[0]);

function prepareCategoriesToImport($categories, $helper)
{
    $categories = is_array($categories) ? $categories : explode(',', $categories);

    $paths = [];
    foreach ($categories as $categoryId) {

        $path = $helper->getCategoryFullPath($categoryId);
        if (!strlen($path)) continue;

        $paths[] = $path;
    }
    return implode(',', $paths);
}


$data = [];
foreach ($csv as $key => $item) {
        $data[$item[0]] = [
            'price' => $item[3],
            'tax_class_id' => $tax[$item[2]],
            'sku' => $item[4],
        ];
}

//var_export($data); die();

$skus = $helper->getProductsIdsBySkus(array_keys($data));

foreach ($skus as $sku => $productId) {

    if (!isset($data[$sku])) continue;

    $helper->updateProductAttributes([$productId], $data[$sku], 0);

}


die();



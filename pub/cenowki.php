<?php

$time_start = microtime(true);

// MAGENTO START
include('../app/bootstrap.php');

use Magento\Framework\App\Bootstrap;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;


$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');


$product = $objectManager->create('Magento\Catalog\Model\Product');
$helper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$collection = $product->getCollection()
    ->addFieldToSelect('*')
    ->setPageSize(48)
    ->setCurPage($page)
    ->addFieldToFilter('type_id', [
            'nin' => ['configurable']
    ]);
    //->setVisibility(4);


$counter = 1;



?>

    <style>

        body {

        }

        .item {
            width: 65mm;
            height: 40mm;
            border: 1px solid #000;
            margin: 5px;
            float: left;
            display: flex;
            flex-direction: column;
            padding: 5px;
        }


        .item:nth-child(12) {
            margin-bottom: 10px;
        }

        .item:nth-child(36) {
            margin-bottom: 20px;
        }

        .break {
            page-break-after: always;
        }

        img {
            width: 100px;
        }

        .top {
            display: flex;
            flex-grow: 1;

        }

        .name {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            display: flex;
            padding-right: 20px;
            height: 100px;
            align-self: center;
        }

        .price {
            font-size: 22px;
            background: steelblue;
            background: rgb(2, 0, 36);
            background: linear-gradient(90deg, rgba(2, 0, 36, 1) 0%, rgba(70, 130, 180, 1) 0%, rgba(183, 200, 215, 1) 100%);
            padding: 10px 20px;
        }

        .price span {
            float: right;
        }

        .noprint {
            clear: both;
            text-align: right;
        }

        @media print {
            .noprint {
                display: none;
            }
        }

    </style>

    <html>
    <body>



    <?php


    foreach ($collection as $pr) {


        if ($counter > 48) break;

        $break = false;
        if ($counter % 3 == 0) {
            $break = true;
        }


        ?>


        <div class="item">
            <div class="top">
                <div class="qr">
                    <img src="https://feeder.wymyslimy.com.pl/media/qrcode/<?= $pr->getId() ?>.png"/>
                </div>
                <div class="name">
                    <?= $pr->getName() ?>
                </div>
            </div>
            <div class="price">
                CENA:<span><?= $helper->currency($pr->getPrice(), true, false) ?></span>
            </div>
        </div>


        <?php


        $counter++;


    }

    ?>


    <div class="noprint">

        <?php
            $to =$page*48;
            $from = $to-47;

        ?>



        <?= $from . '-' .  $to . '/' . $collection->getSize() ?>
    </div>

    </body>
    </html>


<?php


$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start) / 60;

//execution time of the script
//echo "<pre>";
//echo 'Total Execution Time:</b> ' . $execution_time . ' Mins';


//// Create a basic QR code
//$qrCode = new QrCode($pr->getProductUrl());
//$qrCode->setSize(300);
//$qrCode->setMargin(10);
//
//// Set advanced options
//$qrCode->setWriterByName('png');
//$qrCode->setEncoding('UTF-8');
//$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
//$qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
//$qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
////$qrCode->setLabel('Scan the code', 16, __DIR__.'/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER());
//$qrCode->setLogoPath('/home/users/feeder/public_html/magento2/pub/media/logo/stores/1/logo-feder.png');
//$qrCode->setLogoSize(160, 140);
//$qrCode->setValidateResult(false);
//
//// Round block sizes to improve readability and make the blocks sharper in pixel based outputs (like png).
//// There are three approaches:
//$qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN); // The size of the qr code is shrinked, if necessary, but the size of the final image remains unchanged due to additional margin being added (default)
//$qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE); // The size of the qr code and the final image is enlarged, if necessary
//$qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK); // The size of the qr code and the final image is shrinked, if necessary
//
//// Set additional writer options (SvgWriter example)
//$qrCode->setWriterOptions(['exclude_xml_declaration' => true]);
//
//$qrCode->writeFile('/home/users/feeder/public_html/magento2/pub/media/qrcode/' . $pr->getId() . '.png');


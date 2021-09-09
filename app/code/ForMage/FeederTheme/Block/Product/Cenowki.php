<?php

namespace ForMage\FeederTheme\Block\Product;

use ForMage\WholesaleImport\Helper\Data as Helper;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;


class Cenowki extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $product;

    /**
     * @var \Magento\Tax\Api\TaxCalculationInterface
     */
    protected $taxCalculation;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $_helper;


    /**
     * Cenowki constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Api\TaxCalculationInterface $taxCalculation
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Registry $registry
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Api\TaxCalculationInterface $taxCalculation,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Registry $registry,
        Helper $helper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->product = $product;
        $this->taxCalculation = $taxCalculation;
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
    }

    public function getProductTaxRate($product)
    {
        $taxAttribute = $product->getCustomAttribute('tax_class_id');
        return $taxAttribute ? $this->taxCalculation->getCalculatedRate($taxAttribute->getValue()) : '';
    }

    public function getProducts()
    {

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $sku = isset($_GET['sku']) ? (string)$_GET['sku'] : false;
        $csv = isset($_GET['csv']) ? (int)$_GET['csv'] : false;

        $products =  $this->product->getCollection()
        ->addFieldToSelect('*')
        ->setPageSize(20)
        ->setCurPage($page)
        ->addFieldToFilter('type_id', [
            'nin' => ['configurable']
        ]);

        if ($sku) {
            $products->addFieldToFilter('sku', [
                'in' => explode(',', $sku)
            ]);
        }

        if ($csv) {

            $path = BP . '/var/import/cenowki.csv';

            $data = $this->_helper->loadCsv($path, ',') ?? [];
            $skus = [];
            foreach ($data as $item) {
                $skus[] = reset($item);
            }

            $products->addFieldToFilter('sku', [
                'in' => $skus
            ]);
        }

        return $products;
    }

    public function getQrCodeUrl($product)
    {

        $filePath = BP . '/pub/media/qrcode/' . $product->getId() . '.png';

        if (file_exists($filePath)) return 'https://feedersklep.pl/media/qrcode/' . $product->getId() . '.png';

        // Create a basic QR code
        $qrCode = new QrCode(str_replace('https://feeder.wymyslimy.com.pl', 'https://feedersklep.pl', $product->getProductUrl()));
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

// Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
//$qrCode->setLabel('Scan the code', 16, __DIR__.'/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER());
        $qrCode->setLogoPath(BP . '/pub/media/logo/stores/1/logo-feder.png');
        $qrCode->setLogoSize(160, 140);
        $qrCode->setValidateResult(false);

// Round block sizes to improve readability and make the blocks sharper in pixel based outputs (like png).
// There are three approaches:
        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN); // The size of the qr code is shrinked, if necessary, but the size of the final image remains unchanged due to additional margin being added (default)
        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE); // The size of the qr code and the final image is enlarged, if necessary
        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK); // The size of the qr code and the final image is shrinked, if necessary

// Set additional writer options (SvgWriter example)
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

        $qrCode->writeFile($filePath);

        return 'https://feedersklep.pl/media/qrcode/' . $product->getId() . '.png';
    }


}
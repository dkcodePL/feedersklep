<?php

namespace ForMage\WholesaleImport\Model\Wholesale;

use Magento\Catalog\Model\Product\Visibility;
use ForMage\WholesaleImport\Helper\Data as Helper;

abstract class Base extends \Magento\Framework\DataObject implements WholesaleInterface
{
    const CODE = 'data';
    const FILE_PATH = 'import/xml';
    const CSV_FILENAME = 'products.csv';

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_file;

    /**
     * @var \Magento\Framework\Filesystem\Io\Ftp
     */
    protected $_ftp;

    /**
     * @var \ForMage\WholesaleImport\Model\WholesaleFactory
     */
    protected $_wholesaleFactory;

    /**
     * @var array
     */
    protected $data = [];

    protected $multipleSeparator = ',';

    /**
     * @var array
     */
    protected $attributesWithOptions = [];

    /**
     * @var array
     */
    protected $attributesOptions = [];

    protected $arrContextOptions = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
        'http' => [
            'user_agent' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2) Gecko/20100301 Ubuntu/9.10 (karmic) Firefox/3.6'
        ]
    ];

    /**
     * @var string[]
     */
    protected $allowedFields = [
        'sku',
        'name',
        'url_key',
        'description',
        'price',
        'qty',
        'categories',
        'weight',
        'base_image',
        'small_image',
        'thumbnail_image',
        'base_image_label',
        'small_image_label',
        'thumbnail_image_label',
        'additional_images',
        'visibility',
        'product_type',
        'attribute_set_code',
        'product_websites',
        'configurable_variations',
        'related_skus',
        'tax_class_name',
        'additional_attributes'
    ];

    protected $mainProductAttributes = [
        'sku',
        'attribute_set',
        'type',
        'product_websites',
        'category_ids',
        'store',

        'qty',
        'attribute_set_code',
        'categories',

        'name',
        'description',
        'short_description',
        'weight',
        'product_online',
        'tax_class_name',
        'visibility',
        'price',
        'special_price',
        'special_price_from_date',
        'special_price_to_date',
        'url_key',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'base_image',
        'base_image_label',
        'small_image',
        'small_image_label',
        'thumbnail_image',
        'thumbnail_image_label',
        'swatch_image',
        'swatch_image_label',
        'created_at',
        'updated_at',
        'new_from_date',
        'new_to_date',
        'display_product_options_in',
        'map_price',
        'msrp_price',
        'map_enabled',
        'special_price_from_date',
        'special_price_to_date',
        'gift_message_available',
        'custom_design',
        'custom_design_from',
        'custom_design_to',
        'custom_layout_update',
        'page_layout',
        'product_options_container',
        'msrp_price',
        'msrp_display_actual_price_type',
        'map_enabled',
        'country_of_manufacture',
        'map_price',
        'display_product_options_in',
        'related_skus',
        'related_position',
        'crosssell_skus',
        'crosssell_position',
        'upsell_skus',
        'upsell_position',
        'additional_images',
        'additional_image_labels',
        'hide_from_product_page',
        'custom_layout',
        'gallery',
        'has_options',
        'image',
        'image_lable',
        'links_exist',
        'links_purchased_separately',
        'links_title',
        'media_gallery',
        'meta_keyword',
        'minimal_price',
        'msrp',
        'news_from_date',
        'news_to_date',
        'old_id',
        'options_container',
        'price_type',
        'price_view',
        'quantity_and_stock_status',
        'required_options',
        'samples_title',
        'shipment_type',
        'sku_type',
        'special_from_date',
        'special_to_date',
        'status',
        'tax_class_id',
        'thumbnail',
        'thumbnail_label',
        'tier_price',
        'url_path',
        'image_label',
        'weight_type'
    ];


    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * Base constructor.
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Filesystem\Io\Ftp $ftp
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Visibility $productVisibility
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\Io\Ftp $ftp,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Framework\File\Csv $csvProcessor,
        \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Visibility $productVisibility,
        Helper $helper,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->_file = $file;
        $this->_ftp = $ftp;
        $this->_helper = $helper;
        $this->_wholesaleFactory = $wholesale;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->_objectManager = $objectManager;

        $this->multipleSeparator = $helper->getConfigValue(Helper::CONFIG_IMPORT_MULTIPLE_VALUE_SEPARATOR);
    }

    public function getWholeSale()
    {
        return $this->_wholesaleFactory->create()->load($this->getCode(), 'wholesale_code');
    }

    public function getHelper()
    {
        return $this->_helper;
    }

    public function removeExtraFields($data)
    {
        foreach ($data as $field => $items) {
            if (!in_array($field, $this->allowedFields)) unset($data[$field]);
        }
        return $data;
    }

    public function prepareStringForMultiSelect($fieldValue, $array = false)
    {
        $values = $this->removeWhiteSpaces(explode(',', $fieldValue));
        return $array ? $values : implode('<>', $values);
    }

    public function getAllImportedSkus()
    {
        return $this->getHelper()
            ->getProductCollection()
            ->addFieldToFilter('wholesale', $this->getWholeSale()->getWholesaleAttributeId())
            ->getColumnValues('sku');
    }

    /**
     * @param $import
     * @return array
     */
    abstract function getProducts($import): array;

    public function getSku($id, $preSku)
    {
        return $preSku . $id;
    }

    /**
     * @return bool
     */
    abstract function hasStock();

    /**
     * @return bool
     */
    abstract function hasPrices();

    public function getCategories()
    {
        return [];
    }

    public function prepareCategoriesList($categories)
    {
        $categories = array_unique($categories);
        ksort($categories);
        return $categories;
    }

    public function prepareAssignedCategoriesToImport($categories)
    {
        $data = [];
        foreach ($categories ?? [] as $category) {
            $data[$category['wholesale']] = $category['category'];
        }
        return $data;
    }

    public function getFieldsFromFile($code = null)
    {
        return [];
    }

    public function getMeta()
    {
        return [];
    }


    /**
     * @return string
     */
    public function getCode()
    {
        return static::CODE;
    }

    public function getFilePath($filename)
    {
        return $this->getSavePath() . '/' . $filename;
    }

    public function getSavePath()
    {
        return BP . '/var/' . self::FILE_PATH . '/' . $this->getCode();
    }

    /**
     * @param $url
     * @param $filename
     * @param array $credentials
     * @return bool
     */
    public function saveFile($url, $filename = '', $credentials = [])
    {
        $this->checkAndCreateFolder();

        if (!$filename) {
            $array = explode('/', $url);
            $filename = end($array);
        }

        $filePath = $this->getFilePath($filename);

        $ch = curl_init();
        $fp = fopen($filePath, 'w+');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 150);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");

        if (count($credentials)) {
            curl_setopt($ch, CURLOPT_USERPWD, $credentials[0] . ':' . $credentials[1]);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        if (!file_exists($filePath)) {
            return false;
        }
        return true;
    }

    /**
     * @param $credentials
     * @param $files
     * @return bool
     */
    public function saveFileFromFtp($credentials, $files)
    {
        $this->checkAndCreateFolder();

        $ftp = $this->_ftp->open(
            $credentials
        );

        if (!$ftp) {
            return false;
        }

        foreach ($files as $file) {
            try {
                $xml = $this->_ftp->read($file);
                $filePath = $this->getSavePath() . '/' . $file;
                file_put_contents($filePath, $xml);
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        }
        $this->_ftp->close();
    }

    public function ifFileExists($filename)
    {
        return $filename ? file_exists($this->getFilePath($filename)) : false;
    }

    public function getDownloadImagesPath()
    {
        return BP . '/' . $this->getHelper()->getConfigValue(Helper::CONFIG_IMPORT_IMAGES_FILE_DIR) . '/' . $this->getCode();
    }

    public function downloadImage($image)
    {
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($image);
            $filename = $parsed['path'];
        } else {
            $data = explode('/', $image);
            $filename = end($data);
        }

        if (count(explode('.', $filename)) == 1) {
            $filename = $filename . '.jpg';
        }

        try {

            $path = $this->getDownloadImagesPath();
            $filePath = str_replace('//', '/', $path . '/' . $filename);

            $destDir = $this->_file->getDestinationFolder($filePath);
            $this->_file->checkAndCreateFolder($destDir);

            if (file_exists($filePath)) {
                return str_replace('//', '/', $this->getCode() . '/' . $filename);
            }

            $ch = curl_init($image);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $raw = curl_exec($ch);
            curl_close($ch);


            file_put_contents($filePath, $raw);

            return str_replace('//', '/', $this->getCode() . '/' . $filename);

        } catch (\Exception $exception) {
            //echo $exception->getMessage();
            return str_replace('//', '/', $this->getCode() . '/' . $filename);
        }
    }

    public function checkAndCreateFolder($path = null)
    {
        $this->_file->checkAndCreateFolder($path ?? $this->getSavePath());
    }

    public function insertAttributesValues($template, $values)
    {
        preg_match_all("~\{\{\s*(.*?)\s*\}\}~", $template, $attributes);
        if (!$attributes || !isset($attributes[1])) {
            return $this->removeWS($template);
        }
        foreach ($attributes[1] as $attribute) {
            $template = (string)preg_replace("/\{\{(\s+)?($attribute)(\s+)?\}\}/", $this->getValue($this->getFieldsFromFile($attribute), $values), $template);
        }
        return $this->removeWS($template);
    }

    public function removeWS($value)
    {
        return $this->_helper->removeWhiteSpaces($value);
    }

    /**
     * @param $values
     * @return array
     */
    public function removeWhiteSpaces($values): array
    {
        $data = [];
        foreach ($values as $value) {
            $data[] = $this->removeWS($value);
        }
        return $data;
    }

    public function getValue($path, $array, $returnArray = false)
    {

        $temp =& $array;
        foreach ($path as $key) {
            $temp =& $temp[$key];
        }

        $tempArray = $returnArray ? $temp : '';

        return !is_array($temp) ? $temp : $tempArray;
    }


    public function importProducts($products)
    {
        $import = $this->_helper->importProducts($products);
        if (is_array($import) && !empty($import)) {
            foreach ($import as $item) {
                echo "\n" . $item;
            }
        }
    }

    public function addOptionsToAttributes()
    {
        foreach ($this->attributesOptions as $attributeCode => $options) {
            $this->_helper->addOptionsToAttribute($attributeCode, array_unique($options));
        }
    }

    public function getObject($path)
    {
        return $this->_objectManager->create($path);
    }

    public function calculatePrice($price, $multiplier)
    {
        return (float)$price * $multiplier;
    }


}

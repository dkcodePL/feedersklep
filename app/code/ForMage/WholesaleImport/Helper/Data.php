<?php

namespace ForMage\WholesaleImport\Helper;

use ForMage\WholesaleImport\Model\Import\ArraySourceFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\ImportFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\Source\CsvFactory;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\LocalizedException;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const DS = '/';
    const EMPTY_VALUE = '__EMPTY__VALUE__';

    const CONFIG_IMPORT_IMAGES_FILE_DIR = 'wholesaleimport/settings/import_images_file_dir';
    const CONFIG_IMPORT_MULTIPLE_VALUE_SEPARATOR = 'wholesaleimport/settings/import_multiple_value_separator';


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;


    /** @var \Magento\Framework\Model\ResourceModel\Iterator $iterator */
    protected $iterator;

    /**
     * @var \Magento\Store\Api\WebsiteRepositoryInterface
     */
    protected $website;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeOptionManagementInterface
     */
    protected $optionManagement;

    protected $productAttributeRepository;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterface
     */
    protected $attributeOption;

    protected $_directory;

    protected $_mediaDirectory;

    /**
     * @var Import $importFactory
     */
    protected $importFactory;

    /**
     * @var CsvFactory
     */
    private $csvSourceFactory;

    /**
     * @var ArraySourceFactory
     */
    private $arraySourceFactory;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    protected $action;

    /**
     * @var \Magento\Catalog\Model\Product\Gallery\Processor
     */
    protected $processor;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\CategoryLinkRepository
     */
    private $_categoryLinkRepository;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $_categoryRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Tax\Model\TaxClass\Source\ProductFactory
     */
    protected $productTax;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $attributeSet;

    /**
     * Media files uploader
     *
     * @var \Magento\CatalogImportExport\Model\Import\Uploader
     */
    protected $_fileUploader;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Gallery\Processor
     */
    protected $_galleryProcessor;

    protected $_galleryResource;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * Helper to move image from tmp to catalog
     *
     * @var \Magento\Swatches\Helper\Media
     */
    protected $swatchHelper;

    /**
     * @var \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory
     */
    protected $swatchCollectionFactory;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var ProductFactory
     */
    private $product;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    protected $filterGroup;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\CategoryLinkRepository $categoryLinkRepository
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     * @param \Magento\Store\Api\WebsiteRepositoryInterface $website
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \Magento\Catalog\Model\ResourceModel\Product\Action $action
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $optionManagement
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $attributeOption
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param \Magento\Tax\Model\TaxClass\Source\ProductFactory $productTax
     * @param \Magento\Catalog\Model\Product\Gallery\Processor $processor
     * @param \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory
     * @param \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory $collectionFactory
     * @param \Magento\Swatches\Helper\Media $swatchHelper
     * @param \Magento\Catalog\Model\Product\Gallery\Processor $_galleryProcessor
     * @param \Magento\Catalog\Model\ResourceModel\Product\GalleryFactory $_galleryResource
     * @param \Magento\Indexer\Model\IndexerFactory $indexer
     * @param \Magento\Framework\Model\ResourceModel\Iterator $iterator
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param Visibility $productVisibility
     * @param Repository $productAttributeRepository
     * @param ImportFactory $importFactory
     * @param CsvFactory $csvSourceFactory
     * @param ProductFactory $product
     * @param ArraySourceFactory $arraySourceFactory
     * @param ReadFactory $readFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryLinkRepository $categoryLinkRepository,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Store\Api\WebsiteRepositoryInterface $website,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Catalog\Model\ResourceModel\Product\Action $action,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $optionManagement,
        \Magento\Eav\Api\Data\AttributeOptionInterface $attributeOption,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Tax\Model\TaxClass\Source\ProductFactory $productTax,
        \Magento\Catalog\Model\Product\Gallery\Processor $processor,
        \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory,
        \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory $collectionFactory,
        \Magento\Swatches\Helper\Media $swatchHelper,
        \Magento\Catalog\Model\Product\Gallery\Processor $_galleryProcessor,
        \Magento\Catalog\Model\ResourceModel\Product\GalleryFactory $_galleryResource,
        \Magento\Indexer\Model\IndexerFactory $indexer,
        \Magento\Framework\Model\ResourceModel\Iterator $iterator,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        Visibility $productVisibility,
        Repository $productAttributeRepository,
        ImportFactory $importFactory,
        CsvFactory $csvSourceFactory,
        ProductFactory $product,
        ArraySourceFactory $arraySourceFactory,
        ReadFactory $readFactory
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->storeManager = $storeManager;
        $this->website = $website;
        $this->attributeSet = $attributeSet;
        $this->_filesystem = $filesystem;
        $this->filter = $filter;
        $this->action = $action;
        $this->productTax = $productTax;
        $this->csvProcessor = $csvProcessor;
        $this->optionManagement = $optionManagement;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->attributeOption = $attributeOption;
        $this->importFactory = $importFactory;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->arraySourceFactory = $arraySourceFactory;
        $this->readFactory = $readFactory;
        $this->processor = $processor;
        $this->product = $product;
        $this->productRepository = $productRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->iterator = $iterator;
        $this->_categoryLinkRepository = $categoryLinkRepository;
        $this->_uploaderFactory = $uploaderFactory;
        $this->swatchHelper = $swatchHelper;
        $this->swatchCollectionFactory = $collectionFactory;
        $this->_galleryProcessor = $_galleryProcessor;
        $this->_galleryResource = $_galleryResource;
        $this->_indexerFactory = $indexer;
        $this->emulation = $emulation;
        $this->_categoryRepository = $categoryRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfigValue($path, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->_scopeConfig->getValue($path, $scope);
    }

    /**
     * @param $path
     * @param string $delimiter
     * @return mixed
     */
    public function loadCsv($path, $delimiter = ';')
    {
        $this->csvProcessor->setDelimiter($delimiter);
        return $this->csvProcessor->getData($path);
    }

    /**
     * @param $path
     * @param $data
     * @param string $delimiter
     * @return mixed
     */
    public function saveCsv($path, $data, $delimiter = ',')
    {
        $this->csvProcessor->setDelimiter($delimiter);
        return $this->csvProcessor->saveData($path, $data);
    }

    public function getUrlFilter()
    {
        return $this->filter;
    }

    public function getProductBySku($sku)
    {
        try {
            return $this->productRepository->get($sku);

        } catch (\Exception $exception) {

            return false;
        }
    }

    public function getCategoryById($id)
    {
        try {
            return $this->_categoryRepository->get($id);

        } catch (\Exception $exception) {

            return false;
        }
    }

    public function getCategoryCollection()
    {
        return $this->categoryCollectionFactory->create();
    }

    public function saveProduct($product)
    {
        $this->productRepository->save($product);
    }

    public function getProductsIdsBySkus($skus)
    {
        return $this->product
            ->create()
            ->getResource()
            ->getProductsIdsBySkus($skus);
    }

    public function getProductCollection()
    {
        return $this
            ->product
            ->create()
            ->getCollection();
    }

    public function addOptionsToAttribute($attributeCode, $options)
    {
        $newOptionsId = [];
        foreach ($options as $item) {

            try {
                $option = $this->attributeOption;
                $option->setLabel($item);
                $newOptionsId[] = $this->optionManagement->add($attributeCode, $option);
            } catch (\Exception $exception) {

            }
        }

        return $newOptionsId;
    }

    public function getProductAttributeByCode($code)
    {
        try {
            return $this->productAttributeRepository->get($code);

        } catch (\Exception $exception) {

        }
    }


    public function getProductAttributeOptions($attributeCode)
    {
        return $this->optionManagement->getItems($attributeCode);
    }

    public function importProducts($data)
    {
        $import = $this->importFactory->create();
        $import->setData(
            [
                'entity' => 'catalog_product',
                'behavior' => 'append',
                '_import_multiple_value_separator' => $this->getConfigValue(self::CONFIG_IMPORT_MULTIPLE_VALUE_SEPARATOR),
                'validation_strategy' => 'validation-skip-errors',
                'import_images_file_dir' => $this->getConfigValue(self::CONFIG_IMPORT_IMAGES_FILE_DIR),
                'allowed_error_count' => '100000000',
                '_import_empty_attribute_value_constant' => self::EMPTY_VALUE,
            ]
        );

        $arraySource = $this->arraySourceFactory->create(
            ['data' => $data]
        );

        $validate = $import->validateSource($arraySource);
        if (!$validate) {
            return false;
        }

        $result = $import->importSource();
        if (!$result) {
            return false;
        }

        if (!$import->getErrorAggregator()->hasToBeTerminated()) {
            $import->invalidateIndex();
        }

        return $this->getErrorMessages($import);
    }


    public function deleteProducts($data)
    {
        $import = $this->importFactory->create();
        $import->setData(
            [
                'entity' => 'catalog_product',
                'behavior' => 'delete',
                'validation_strategy' => 'validation-skip-errors',
                'allowed_error_count' => '100000000',
            ]
        );

        $arraySource = $this->arraySourceFactory->create(
            ['data' => $data]
        );

        $validate = $import->validateSource($arraySource);
        if (!$validate) {
        }

        $import->importSource();

        if (!$import->getErrorAggregator()->hasToBeTerminated()) {
            $import->invalidateIndex();
        }

        return $this->getErrorMessages($import);

    }

    public function importStocks($data)
    {
        $import = $this->importFactory->create();
        $import->setData(
            [
                'entity' => 'stock_sources',
                'behavior' => 'append',
                'validation_strategy' => 'validation-skip-errors',
                'allowed_error_count' => '100000000',
            ]
        );

        $arraySource = $this->arraySourceFactory->create(
            ['data' => $data]
        );

        $validate = $import->validateSource($arraySource);
        if (!$validate) {
        }

        $import->importSource();

        if (!$import->getErrorAggregator()->hasToBeTerminated()) {
            $import->invalidateIndex();
        }

    }

    /**
     * @param $sku
     * @param $images
     */
    public function addImagesToProduct($sku, $images)
    {
        $product = $this->getProductBySku($sku);
        if (!$product || !$product->getId()) return false;

        $mediaDirectory = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        $product->getMediaGalleryEntries();

        $this->deleteImagesFromProduct($product);

        foreach ($images as $image) {

            $uploadedImage = $this->uploadMediaFiles($image);

            $imagePath = $mediaDirectory->getAbsolutePath('catalog/product') . $uploadedImage;

            if (!file_exists($imagePath)) continue;

            $this->_gallery->addImage($product, 'catalog/product' . $uploadedImage, ['base', 'thumbnail', 'small_image'], false, false);
        }

        $this->productRepository->save($product);

    }

    public function deleteImagesFromProduct($product, $deleteFiles = false)
    {
        $mediaDirectory = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        $this->processor->clearMediaAttribute($product, ['image', 'small_image', 'thumbnail', 'swatch_image']);

        $ids = [];
        $mediaGallery = $product->getMediaGalleryImages();
        foreach ($mediaGallery as $key => $child) {

            $ids[] = $key;

            $file = $child->getFile();
            $this->processor->removeImage($product, $file);

            $imagePath = $mediaDirectory->getAbsolutePath('catalog/product') . $file;
            if ($deleteFiles && file_exists($imagePath)) unlink($imagePath);

        }

        $product->setMediaGalleryEntries(null);

        if ($product->getTypeId() == 'configurable') {

            $this->saveProduct($product);

            $this->_galleryResource->create()->deleteGallery($ids);
        }

    }


    /**
     * Uploading files into the "catalog/product" media folder.
     *
     * Return a new file name if the same file is already exists.
     *
     * @param string $fileName
     * @param bool $renameFileOff [optional] boolean to pass.
     * Default is false which will set not to rename the file after import.
     * @return string
     */
    protected function uploadMediaFiles($fileName, $renameFileOff = false)
    {
        try {
            $res = $this->_getUploader()->move($fileName, $renameFileOff);
            return $res['file'];
        } catch (\Exception $e) {

            echo $e->getMessage();
        }
    }

    /**
     * @param string $destinationDir
     * @return \Magento\CatalogImportExport\Model\Import\Uploader
     */
    protected function _getUploader($destinationDir = "catalog/product")
    {
        if ($this->_fileUploader === null) {
            $this->_fileUploader = $this->_uploaderFactory->create();

            $this->_fileUploader->init();

            $dirConfig = DirectoryList::getDefaultConfig();
            $dirAddon = $dirConfig[DirectoryList::MEDIA][DirectoryList::PATH];

            if (!empty($this->_parameters[Import::FIELD_NAME_IMG_FILE_DIR])) {
                $tmpPath = $this->_parameters[Import::FIELD_NAME_IMG_FILE_DIR];
            } else {
                $tmpPath = $dirAddon . '/' . $this->_mediaDirectory->getRelativePath('import');
            }

            if (!$this->_fileUploader->setTmpDir($tmpPath)) {
                throw new LocalizedException(
                    __('File directory \'%1\' is not readable.', $tmpPath)
                );
            }

            $destinationPath = $dirAddon . '/' . $this->_mediaDirectory->getRelativePath($destinationDir);

            $this->_mediaDirectory->create($destinationPath);
            if (!$this->_fileUploader->setDestDir($destinationPath)) {
                throw new LocalizedException(
                    __('File directory \'%1\' is not writable.', $destinationPath)
                );
            }
        }
        return $this->_fileUploader;
    }

    public function getErrorMessages($import)
    {
        $errorAggregator = $import->getErrorAggregator();
        return array_map(function (ProcessingError $error) {
            return sprintf(
                'Line %s: %s %s',
                $error->getRowNumber() ?: '[?]',
                $error->getErrorMessage(),
                $error->getErrorDescription()
            );
        }, $errorAggregator->getAllErrors());
    }

    public function reindexByIndex($index)
    {
        $this->_indexerFactory->create()->load($index)->reindexAll();
    }


    protected function iterateCollection($collection, $callback)
    {
        $this
            ->iterator
            ->walk(
                $collection->getSelect(),
                [[$this, $callback]]
            );
    }

    public function addProductToCategory($args)
    {
        try {

            $product = $this->productRepository->getById($args['row']['entity_id']);

            if (count($product->getCategoryIds()) > 0) return;

            $product->setCategoryIds(explode(',', $this->getConfigValue(self::CONFIG_STORE_EMPTY_CATEGORY)));
            $this->productRepository->save($product);

        } catch (\Exception$exception) {

        }
    }

    /**
     * @param $fileName
     * @param bool $renameFileOff
     * @return bool
     */
    protected function uploadSwatchImage($fileName, $renameFileOff = false)
    {
        try {
            $res = $this->_getUploader('attribute/swatch')->move($fileName, $renameFileOff);
            return $res['file'];
        } catch (\Exception $e) {

            return false;
        }
    }

    /**
     * Load swatch if it exists in database
     *
     * @param int $optionId
     * @param int $storeId
     * @return Swatch
     */
    protected function loadSwatchIfExists($optionId, $storeId)
    {
        $collection = $this->swatchCollectionFactory->create();
        $collection->addFieldToFilter('option_id', $optionId);
        $collection->addFieldToFilter('store_id', $storeId);
        $collection->setPageSize(1);

        return $collection->getFirstItem();
    }

    public function getAttributeSetNameById($id)
    {
        try {
            return $this->attributeSet->get($id);

        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getProductTaxClass()
    {
        return $this->productTax->create();
    }

    public function getWebsitesCodesById($ids)
    {
        $websites = [];
        foreach ($ids as $storeId) {

            $websiteId = (int)$this->storeManager->getStore($storeId)->getWebsiteId();

            $websites[] = $this->website->getById($websiteId)->getCode();
        }
        return array_unique($websites);
    }

    public function getCategoryFullPath($categoryId)
    {
        $category = $this->getCategoryById($categoryId);
        if (!$category) return '';

        $path = [];
        $categories = $this->getCategoryCollection()
            ->addFieldToSelect('name')
            ->addIdFilter($category->getParentIds());
        foreach ($categories as $parentCategory) {
            if ($parentCategory->getLevel() == 0) continue;
            $path[] = $parentCategory->getName();
        }
        $path[] = $category->getName();

        return implode('/', $path);
    }

    /**
     * @param $products
     * @return mixed
     */
    public function correctVisibility($products)
    {
        foreach ($products as &$product) {

            if (isset($product['visibility']) && is_numeric($product['visibility'])) {
                $product['visibility'] = (string)$this->productVisibility->getOptionText($product['visibility']);
            }

        }

        return $products;
    }

    public function removeWhiteSpaces($text)
    {
        $text = preg_replace('/[\t\n\r\0\x0B]/', '', $text);
        $text = preg_replace('/([\s])\1+/', ' ', $text);
        $text = trim($text);
        return $text;
    }

    /**
     * @param array $ids
     * @param array $attributes
     * @param int $storeId
     */
    public function updateProductAttributes($ids, $attributes, $storeId = 0)
    {
        $this->action->updateAttributes($ids, $attributes, $storeId);
    }

    /**
     * @param $prices
     * @return mixed
     */
    public function updatePrices($prices)
    {
        $skus = $this->getProductsIdsBySkus(array_keys($prices));
        foreach ($skus as $sku => $productId) {

            if (!isset($prices[$sku])) continue;

            $update = [
                'price' => $prices[$sku]['price']
            ];

            $this->action->updateAttributes([$productId], $update, 0);
            unset($prices[$sku]);
        }
        return $prices;
    }


}

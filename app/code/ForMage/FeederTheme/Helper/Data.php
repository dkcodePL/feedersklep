<?php

namespace ForMage\FeederTheme\Helper;

use Magento\Swatches\Helper;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeOptionManagementInterface
     */
    protected $optionManagement;

    /**
     * @var Helper\Data
     */
    protected $_swatchesHelper;

    /**
     * @var Helper\Media
     */
    protected $_swatchesMediaHelper;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $_categoryRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    protected $getStockItemConfiguration;
    protected $productSalableQty;
    protected $stockResolver;
    protected $storeManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Helper\Data $swatchwesHelper
     * @param Helper\Media $swatchwesMediaHelper
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param GetProductSalableQtyInterface $productSalableQty
     * @param StockResolverInterface $stockResolver
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $optionManagement
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Helper\Data $swatchwesHelper,
        Helper\Media $swatchwesMediaHelper,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        GetProductSalableQtyInterface $productSalableQty,
        StockResolverInterface $stockResolver,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $optionManagement
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->optionManagement = $optionManagement;
        $this->_categoryRepository = $categoryRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->_swatchesHelper = $swatchwesHelper;
        $this->_swatchesMediaHelper = $swatchwesMediaHelper;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->productSalableQty = $productSalableQty;
        $this->stockResolver = $stockResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $path
     * @return mixed
     */

    public function getConfigValue($path, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->_scopeConfig->getValue($path, $scope);
    }

    public function hasSpecialPrice($_product)
    {

        if ($_product->getPrice() > $_product->getFinalPrice()) return true;

        $specialprice = $_product->getSpecialPrice();
        if (!$specialprice) return false;

        $orgprice = $_product->getPrice();
        if ($specialprice > $orgprice) return false;

        $specialfromdate = $_product->getSpecialFromDate();
        $specialtodate = $_product->getSpecialToDate();
        $today = time();
        if ((is_null($specialfromdate) && is_null($specialtodate)) || ($today >= strtotime($specialfromdate) && is_null($specialtodate)) || ($today <= strtotime($specialtodate) && is_null($specialfromdate)) || ($today >= strtotime($specialfromdate) && $today <= strtotime($specialtodate))) {
            return true;
        }
        return false;
    }

    public function isNew($_product)
    {
        $newsfromdate = $_product->getNewsFromDate();
        $newstodate = $_product->getNewsToDate();
        $today = time();

        if ((is_null($newsfromdate) && is_null($newstodate))) {
            return false;
        }

        if (($today >= strtotime($newsfromdate) && is_null($newstodate)) || ($today <= strtotime($newstodate) && is_null($newsfromdate)) || ($today >= strtotime($newsfromdate) && $today <= strtotime($newstodate))) {
            return true;
        }

        return false;
    }

    /**
     * @param string $attributeCode
     * @return array
     */
    public function getProductAttributeOptions($attributeCode)
    {
        $options = [];
        foreach ($this->optionManagement->getItems($attributeCode) as $option) {
            $options[$option->getValue()] = $option->getLabel();
        }
        return $options;
    }


    public function getCategoryCollection()
    {
        return $this->categoryCollectionFactory->create();
    }

    public function getCategoryById($id)
    {
        try {
            return $this->_categoryRepository->get($id);

        } catch (\Exception $exception) {

            return false;
        }
    }

    public function getSwatchImage($model)
    {
        if ($model->getManufacturer()) {
            $image = $this->_swatchesHelper->getSwatchesByOptionsId([$model->getManufacturer()]);
            if (current($image)['value']) {
                return $this->_swatchesMediaHelper->getSwatchAttributeImage('swatch_thumb', current($image)['value']);
            }
        }

        return null;
    }

    public function getProductQty($product)
    {
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        $stock = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
        $stockId = $stock->getStockId();

        return $this->productSalableQty->execute($product->getSku(), $stockId);
    }


}

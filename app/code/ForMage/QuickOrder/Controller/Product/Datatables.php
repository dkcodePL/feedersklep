<?php
/*
 *  4mage Package
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the 4mage.co license that is
 *  available through the world-wide-web at this URL:
 *  https://4mage.co/license-agreement/
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade this extension to newer
 *  version in the future.
 *
 *  @category 	ForMage
 *  @package 	ForMage_QuickOrder
 *  @copyright 	Copyright (c) 2020 4mage.co (https://4mage.co/)
 *  @license  	https://4mage.co/license-agreement/
 *
 */

namespace ForMage\QuickOrder\Controller\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Helper\Data as TaxHelper;
use ForMage\QuickOrder\Helper\Data as Helper;

class Datatables extends \Magento\Framework\App\Action\Action
{

    /**
     * @var Helper
     */
    protected $_helper;

    protected $taxHelper;

    /**
     * @var \Magento\Tax\Api\TaxCalculationInterface
     */
    protected $taxCalculation;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collection;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface $productFactory
     */
    protected $productRepository;

    /**
     * @var \Smile\ElasticsuiteCore\Search\Request\Builder
     */
    protected $builder;

    /**
     * @var \Magento\Search\Model\SearchEngine
     */
    protected $searchEngine;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $products;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    protected $imageHelper;

    protected $block;

    protected $productAttributes = [];

    /**
     * Datatables constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collection
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param Visibility $productVisibility
     * @param \ForMage\QuickOrder\Block\Product\Index $block
     * @param \Smile\ElasticsuiteCore\Search\Request\Builder $builder
     * @param \Magento\Search\Model\SearchEngine $searchEngine
     * @param \Magento\Tax\Api\TaxCalculationInterface $taxCalculation
     * @param TaxHelper $taxHelper
     * @param Helper $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Helper\Image $imageHelper,
        Visibility $productVisibility,
        \ForMage\QuickOrder\Block\Product\Index $block,
        \Smile\ElasticsuiteCore\Search\Request\Builder $builder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Tax\Api\TaxCalculationInterface $taxCalculation,
        TaxHelper $taxHelper,
        Helper $helper
    )
    {
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->collection = $collection;
        $this->productRepository = $productRepository;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->builder = $builder;
        $this->_urlBuilder = $urlBuilder;
        $this->searchEngine = $searchEngine;
        $this->_helper = $helper;
        $this->taxHelper = $taxHelper;
        $this->taxCalculation = $taxCalculation;
        $this->imageHelper = $imageHelper;
        $this->block = $block;

        $this->productAttributes = $helper->getProductAttributes();
    }

    public function execute()
    {
        $post = $this->getRequest()->getPost();

        $response = [
            'data' => [],
            'draw' => (int)$post['draw'],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ];

        if (!$this->_helper->isAllowed()) {
            return $this->resultJsonFactory->create()->setData($response);
        }

        try {

            $this->setCollection();


            $this->sortCollection($post);

            $size = $this->products->getSize();

            $this->filterCollection($post);

            $cloned = clone $this->products;

            // pagination
            $limit = $post['length'];
            $page = $post['start'] ? $post['start'] : 0;

            $this->products->getSelect()->limit($limit, $page);

            $response['data'] = $this->getData();
          //  $response['sql'] = $this->products->getSelect()->__toString();
            $response['recordsTotal'] = $size;
            $response['recordsFiltered'] = count($cloned);

        } catch (\Exception $exception) {
            $this->_messageManager->addErrorMessage($exception->getMessage());
        }

        return $this->resultJsonFactory->create()->setData($response);
    }

    protected function getData()
    {
        $data = [];
        foreach ($this->products as $item) {

            $temp = [
                'name' => $item->getName(),
                'price' => $item->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount(),
                'price2' => $this->getProductPriceWithTax($item),
                'tax_rate' => $this->getProductTaxRate($item),
                'qty' => $this->getQtyBySku($item->getSku()),
                'sku' => $item->getSku(),
                'saleable' => $item->isSaleable(),
                'img' => $this->imageHelper->init($item, 'product_page_image_small')
                    ->setImageFile($item->getSmallImage())
                    ->resize(380)
                    ->getUrl(),
                'cartUrl' => $this->block->getAddToCartPostParams($item),
            ];

            foreach ($this->productAttributes as $attributeCode) {

                $value = $item->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($item);

                $temp[$attributeCode] =  $value !== false ? $value : '';
            }

            $data[] = $temp;

        }
        return $data;
    }

    protected function setCollection()
    {
        $this->products = $this->collection
            ->create()
            ->addFieldToFilter('status', ['in', $this->productStatus->getVisibleStatusIds()])
            ->addFieldToFilter('visibility', ['in', $this->productVisibility->getVisibleInSiteIds()])
            ->addFinalPrice()
            ->addAttributeToSelect(array_merge(['name', 'small_image', 'manufacturer'], $this->productAttributes));

    }

    protected function sortCollection($post)
    {
        // order by
        if (!isset($post['order'][0]['column']) || !$post['order'][0]['column'] || !isset($post['order'][0]['dir'])) {

            $this->products->setOrder(
                'main_table.entity_id',
                'desc'
            );

            return;
        }

        $sortBy = $post['columns'][$post['order'][0]['column']]['data'];
        $dir = $post['order'][0]['dir'];

        switch ($sortBy) {
            case 'price2':
                $sortBy = 'price';
                break;
        }

        $this->products->setOrder($sortBy, $dir);
    }

    protected function filterCollection($post)
    {
        $searchValue = isset($post['search']['value']) ? $post['search']['value'] : false;
        if (!$searchValue) return;

        $searchRequest = $this->builder->create(
            $this->storeManager->getStore()->getId(),
            'catalog_product_autocomplete',
            0,
            500,
            $searchValue
        // $sortOrders
        //   $filters,
        // $queryFilters
        );

        $queryResponse = $this->searchEngine->search($searchRequest);

        // Filter search results. The pagination has to be resetted since it is managed by the engine itself.
        $docIds = array_map(
            function (\Magento\Framework\Api\Search\Document $doc) {
                return (int)$doc->getId();
            },
            $queryResponse->getIterator()->getArrayCopy()
        );

        if (empty($docIds)) {
            $docIds = [0];
        }

        $this->products->addIdFilter($docIds);


        // todo: add event


        foreach ($post['columns'] as $column) {

            if (!$column['searchable'] || strlen($column['search']['value']) < 1) {
                continue;
            }

            $value = $column['search']['value'];

            $this->products->addFieldToFilter('main_table.' . $column['data'], ['eq' => $value]);

        }


    }

    protected function getQtyBySku($sku)
    {
        $html = '';
        $sources = $this->_helper->getSourceItemsBySku($sku);
        foreach ($sources as $item) {

            $source = $this->_helper->getSourceByCode($item->getSourceCode());

            $qty = $item->getQuantity() < 0 ? 0 : $item->getQuantity();
            //$qty = $qty > 100 ? '100+' : $qty;
            $html .= $source->getName() . ': ' . $qty . "\n";

        }

        return $html;
    }

    protected function getProductPriceWithTax($product)
    {
        return $this->taxHelper->getTaxPrice($product, $product->getFinalPrice(), true);
    }

    protected function getProductTaxRate($product)
    {
        $taxAttribute = $product->getCustomAttribute('tax_class_id');
        return $taxAttribute ? $this->taxCalculation->getCalculatedRate($taxAttribute->getValue()) : '';
    }

}
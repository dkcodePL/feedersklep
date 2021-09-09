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

namespace ForMage\QuickOrder\Helper;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Http\Context;

class Data extends \ForMage\Base\Helper\Data
{
    const ENABLED = 'quickorder/base/enabled';
    const ATTRIBUTES = 'quickorder/settings/attributes';
    const ALLOWED_GROUPS = 'quickorder/settings/allowed';

    /**
     * @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface
     */
    private $_sourceItemsBySku;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    private $_sourceRepository;

    /**
     * @var \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite
     */
    private $_stockWebsite;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeOptionManagementInterface
     */
    private $optionManagement;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Context
     */
    private $httpContext;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $stockWebsite
     * @param \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $optionManagement
     * @param ResourceConnection $resourceConnection
     * @param Context $httpContext
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $stockWebsite,
        \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $optionManagement,
        ResourceConnection $resourceConnection,
        Context $httpContext
    )
    {
        parent::__construct($context);
        $this->httpContext = $httpContext;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_sourceItemsBySku = $sourceItemsBySku;
        $this->_sourceRepository = $sourceRepository;
        $this->_stockWebsite = $stockWebsite;
        $this->resourceConnection = $resourceConnection;
        $this->optionManagement = $optionManagement;
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        $allowed = $this->getAllowedGCustomerGroups();
        if (empty($allowed)) return true;

        $currentCustomerGroup = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);

        return in_array($currentCustomerGroup, $allowed);
    }

    /**
     * @return array
     */
    public function getAllowedGCustomerGroups()
    {
        return array_filter(explode(',', $this->getConfigValue(self::ALLOWED_GROUPS)), 'strlen');
    }

    public function _getSourceCodes($stockId)
    {
        $tableName = $this->resourceConnection->getTableName(\Magento\Inventory\Model\ResourceModel\StockSourceLink::TABLE_NAME_STOCK_SOURCE_LINK);
        $connection = $this->resourceConnection->getConnection();

        $qry = $connection
            ->select()
            ->distinct()
            ->from($tableName, 'source_code')
            ->where('stock_id = (?)', $stockId);

        return $connection->fetchCol($qry);
    }

    public function getSourceItemsBySku($sku)
    {
        return $this->_sourceItemsBySku->execute($sku);
    }

    public function getCurrentStockId()
    {
        return $this->_stockWebsite->execute();
    }

    public function getAssignedSourcesByStockId($stockId)
    {
        return $this->_getSourceCodes($stockId);
    }

    public function getSourceByCode($sourceCode)
    {
        return $this->_sourceRepository->get($sourceCode);
    }

    public function getProductAttributes()
    {
        return explode(',', $this->getConfigValue(self::ATTRIBUTES));
    }

    /**
     * @param $attributeCode
     * @return array
     */
    public function getProductAttributeOptions($attributeCode)
    {
        $options = [];
        foreach ($this->optionManagement->getItems($attributeCode) as $option) {

            $options[] = [
                'label' => $option->getLabel(),
                'value' => $option->getValue(),
            ];

        }
        return $options;
    }


}

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
 *  @package 	ForMage_HidePrices
 *  @copyright 	Copyright (c) 2020 4mage.co (https://4mage.co/)
 *  @license  	https://4mage.co/license-agreement/
 *
 */

namespace ForMage\HidePrices\Setup;

use Magento\Framework\Setup\{
    ModuleContextInterface,
    ModuleDataSetupInterface,
    UpgradeDataInterface
};
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use ForMage\HidePrices\Helper\Data as Helper;


class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addProductFields($setup);
        }

        $setup->endSetup();
    }

    public function addProductFields(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, Helper::HIDE_PRICE, [
            'group' => 'Hide Prices',
            'type' => 'varchar',
            'label' => 'Hide Prices',
            'input' => 'multiselect',
            'source' => \ForMage\HidePrices\Model\Product\Attribute\Source\Group::class,
            'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
            'used_in_product_listing' => true,
            'required' => false,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'is_unique' => 0,
            'system' => 0,
            'wysiwyg_enabled' => false,
            'sort_order' => 10,
            'user_defined' => 0,
            'visible_on_front' => 0,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE
        ]);

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, Helper::HIDE_ADD_TO_CART, [
            'group' => 'Hide Prices',
            'type' => 'varchar',
            'label' => 'Hide Add to Cart',
            'input' => 'multiselect',
            'source' => \ForMage\HidePrices\Model\Product\Attribute\Source\Group::class,
            'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
            'used_in_product_listing' => true,
            'required' => false,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'is_unique' => 0,
            'system' => 0,
            'wysiwyg_enabled' => false,
            'sort_order' => 10,
            'user_defined' => 0,
            'visible_on_front' => 0,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE
        ]);
    }

}
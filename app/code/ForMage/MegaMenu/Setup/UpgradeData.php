<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \ForMage\MegaMenu\Model\ResourceModel\MenuFactory
     */
    protected $_menuFactory;

    /**
     * UpgradeData constructor.
     * @param \ForMage\MegaMenu\Model\ResourceModel\MenuFactory $menuFactory
     */
    public function __construct(
        \ForMage\MegaMenu\Model\ResourceModel\MenuFactory $menuFactory
    )
    {
        $this->_menuFactory = $menuFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->createRootMenu($setup);
        }
        $setup->endSetup();
    }

    protected function createRootMenu(ModuleDataSetupInterface $setup)
    {
        $menu = $this->_menuFactory->create();
        $menu->insertData(
            [
                [
                    'path' => 1,
                    'is_active' => 1,
                    'position' => 0,
                    'level' => 0,
                    'parent_id' => 0,
                    'name' => 'Root',
                    'children_count' => 1
                ],
                [
                    'path' => '1/2',
                    'is_active' => 1,
                    'position' => 1,
                    'level' => 1,
                    'parent_id' => 1,
                    'store_ids' => 0,
                    'name' => 'Main',
                    'children_count' => 0
                ]
            ]
        );
    }
}
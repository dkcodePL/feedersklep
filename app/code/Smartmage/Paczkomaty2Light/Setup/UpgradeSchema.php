<?php

namespace Smartmage\Paczkomaty2Light\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()
            ->addColumn(
                $setup->getTable('quote'),
                'paczkomaty_target_point',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 32,
                    'default' => '',
                    'comment' => 'Wybrana maszyna'
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable('sales_order'),
                'paczkomaty_data',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 512,
                    'default' => '',
                    'comment' => 'Paczkomaty data'
                ]
            );

        $setup->endSetup();
    }
}
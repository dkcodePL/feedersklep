<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->createSlideTable($setup);
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->upgradeSlideTable($setup);
        }
    }

    protected function createSlideTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('4mage_slide'))
            ->addColumn(
                'slide_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id'
            )->addColumn(
                'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Name'
            )->addColumn(
                'store_ids', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Store Ids'
            )->addColumn(
                'group_ids', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Group Ids'
            )->addColumn(
                'position', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Position'
            )->addColumn(
                'url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Url'
            )->addColumn(
                'image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Image'
            )->addColumn(
                'image_as_bg', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0', 'unsigned' => true], 'Image as bg'
            )->addColumn(
                'html', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable' => false], 'Html'
            )->addColumn(
                'is_active', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0', 'unsigned' => true], 'Is Active'
            )->addColumn(
                'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false], 'Date'
            )->addColumn(
                'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => false], 'Date'
            );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();

        return $this;
    }

    protected function upgradeSlideTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('4mage_slide');

        $connection = $installer->getConnection();

        $columns = [
            'from_date' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'nullable' => false,
                'length' => null,
                'comment' => 'From Date',
            ],
            'to_date' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'nullable' => false,
                'length' => null,
                'comment' => 'To Date',
            ],

        ];

        foreach ($columns as $name => $definition) {
            $connection->addColumn($tableName, $name, $definition);
        }

        $installer->endSetup();
        return $this;
    }


}

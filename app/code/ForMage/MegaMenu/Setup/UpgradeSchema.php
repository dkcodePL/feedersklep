<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Cms module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->createMegaMenuTable($setup);
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->upgradeTable1($setup);
        }
    }

    protected function createMegaMenuTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('4mage_menu'))
            ->addColumn(
                'menu_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id'
            )->addColumn(
                'type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Type'
            )->addColumn(
                'type_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Type Id'
            )->addColumn(
                'class', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Class'
            )->addColumn(
                'drop_down', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0', 'unsigned' => true], 'Drop Down'
            )->addColumn(
                'columns', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0', 'unsigned' => true], 'Columns'
            )->addColumn(
                'store_ids', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Stores'
            )->addColumn(
                'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Name'
            )->addColumn(
                'custom_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Custom Name'
            )->addColumn(
                'left_block', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Left Block'
            )->addColumn(
                'right_block', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Right Block'
            )->addColumn(
                'top_block', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Top Block'
            )->addColumn(
                'bottom_block', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Bottom Block'
            )->addColumn(
                'custom_url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Url'
            )->addColumn(
                'custom_css', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable' => false], 'CSS'
            )->addColumn(
                'description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable' => false], 'Description'
            )->addColumn(
                'path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Path'
            )->addColumn(
                'position', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Position'
            )->addColumn(
                'level', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Level'
            )->addColumn(
                'children_count', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Children Count'
            )->addColumn(
                'parent_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Parent Id'
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

    protected function upgradeTable1(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('4mage_menu');

        $connection = $installer->getConnection();

        $columns = [
            'full_width' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'unsigned' => true,
                'comment' => 'Full Width',
            ]

        ];

        foreach ($columns as $name => $definition) {
            $connection->addColumn($tableName, $name, $definition);
        }

        $installer->endSetup();
        return $this;
    }



}
